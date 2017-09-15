<?php

namespace Tylercd100\License;

use Tylercd100\License\Traits\HasLicenses;
use Tylercd100\License\Models\License as LicenseModel;
use Tylercd100\License\Exceptions\LicenseException;

abstract class License
{
    /**
     * Owner of the licenses
     *
     * @var HasLicenses
     */
    protected $owner;

    /**
     * The database model for tracking licenses
     *
     * @var LicenseModel
     */
    protected $model;
    
    function __construct(HasLicenses $owner)
    {
        $this->owner = $owner;
        $this->model = LicenseModel::firstOrCreate([
            "owner_type" => get_class($owner),
            "owner_id" => $owner->id,
            "maintainer" => get_class($this),
        ], [
            "quantity" => 0,
        ]);
    }

    /**
     * Throws exception if there are not enough licenses available
     *
     * @param [type] $quantity
     * @return void
     */
    public function check($quantity)
    {
        $remaining = $this->remaining();
        if ($remaining > $quantity) {
            throw new LicenseException($this->message($remaining, $quantity));
        }
    }

    /**
     * Returns the difference between the maximum amount licenses and what you are trying to limit
     *
     * @return int
     */
    public function remaining()
    {
        return $this->maximum() - $this->used();
    }

    /**
     * Returns the maximum amount of licenses
     *
     * @return int
     */
    public function maximum()
    {
        return $this->model->quantity;
    }

    /**
     * Returns the human readable error string when there are not enough licenses available.
     *
     * @param int $remaining Number of licenses available.
     * @param int $quantity Number of licenses trying to allocate.
     * @return string
     */
    protected function message($remaining, $quantity)
    {
        return "There are not enough licenses available. Tried to allocate {$quantity} but there are only {$remaining} available.";
    }

    /**
     * Returns the current amount of licenses in use
     *
     * @return int
     */
    abstract public function used();

    /**
     * Called before adding to the license count.
     * 
     * Should return true to proceed or false to cancel operation
     * 
     * @return boolean
     */
    abstract protected function adding();

    /**
     * Called after adding to the license count
     *
     * @return void
     */
    abstract protected function added();

    /**
     * Called before subtracting the license count
     *
     * @return void
     */
    abstract protected function subtracting();

    /**
     * Called after subtracting the license count
     * 
     * Should return true to proceed or false to cancel operation
     *
     * @return void
     */
    abstract protected function subtracted();

    /**
     * Add more licenses
     *
     * @param int $quantity
     * @return boolean
     */
    final public function add($quantity)
    {
        if (!is_int($quantity) || $quantity < 0) {
            throw new LicenseExeception("Quantity must be a positive integer.");
        }

        $this->adding($quantity);

        $this->model->quantity += $quantity;
        $this->model->save();

        $this->added($quantity);
        
        return true;
    }

    /**
     * Subtract licenses
     *
     * @param int $quantity
     * @return boolean
     */
    final public function sub($quantity)
    {
        if (!is_int($quantity) || $quantity < 0) {
            throw new LicenseExeception("Quantity must be a positive integer.");
        }

        if ($this->model->quantity - $quantity < 0) {
            throw new LicenseExeception("You cannot remove more licenses than you have available.");
        }

        $this->subtracting($quantity);

        $this->model->quantity -= $quantity;
        $this->model->save();

        $this->subtracted($quantity);
        
        return true;
    }
}