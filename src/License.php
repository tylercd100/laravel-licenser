<?php

namespace Tylercd100\License;

use Illuminate\Database\Eloquent\Model;
use Tylercd100\License\Exceptions\LicenseException;
use Tylercd100\License\Models\License as LicenseModel;
use Tylercd100\License\HasLicenses;

abstract class License
{
    /**
     * Owner of the licenses
     *
     * @var Model
     */
    protected $owner;

    /**
     * The database model for tracking licenses
     *
     * @var LicenseModel
     */
    protected $model;

    /**
     * The default starting amount for the license
     *
     * @var int
     */
    protected $default = 0;

    function __construct(Model $owner)
    {
        if (!in_array(HasLicenses::class, class_uses($owner))) {
            throw new LicenseException("The owner must use the trait: ".HasLicenses::class);
        }

        $this->model = $this->getModel($owner);
        $this->owner = $owner;
    }

    protected function getModel(Model $owner)
    {
        $opts = [
            "owner_type" => get_class($owner),
            "owner_id" => $owner->id,
            "license" => get_class($this),
        ];

        $x = LicenseModel::where($opts)->first();
        
        if (!$x) {
            $opts["quantity"] = $this->default;
            $x = LicenseModel::create($opts);
        }

        return $x;
    }

    /**
     * Throws exception if there are not enough licenses available
     *
     * @param int $quantity
     * @param boolean $add
     * @return void
     */
    final public function allocate($quantity, $add = false)
    {
        $remaining = $this->remaining();
        if ($remaining < $quantity) {
            if(!$add) {
                $this->error($remaining, $quantity);
            } else {
                $this->add($quantity - $remaining);
            }
        }

        $this->success($remaining, $quantity);
    }
    
    /**
     * Called when there are enough licenses available
     *
     * @param int $quantity
     * @return void
     */
    protected function success($quantity)
    {
        
    }

    /**
     * Called when there are not enough licenses available
     *
     * @param int $remaining
     * @param int $quantity
     * @return void
     */
    protected function error($remaining, $quantity)
    {
        throw new LicenseException($this->message($remaining, $quantity));
    }

    /**
     * Returns the difference between the maximum amount licenses and what you are trying to limit
     *
     * @return int
     */
    public function remaining()
    {
        return max($this->maximum() - $this->used(), 0);
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
     * Returns human readable string for this license
     *
     * @return string
     */
    abstract public function name();

    /**
     * Returns the current amount of licenses in use
     *
     * @return int
     */
    abstract public function used();

    /**
     * Called before adding to the license count.
     * 
     * @param int $quantity
     * @return void
     */
    abstract protected function adding($quantity);

    /**
     * Called after adding to the license count
     *
     * @param int $quantity
     * @return void
     */
    abstract protected function added($quantity);

    /**
     * Called before subtracting the license count
     *
     * @param int $quantity
     * @return void
     */
    abstract protected function subtracting($quantity);

    /**
     * Called after subtracting the license count
     * 
     * @param int $quantity
     * @return void
     */
    abstract protected function subtracted($quantity);

    /**
     * Add more licenses
     *
     * @param int $quantity
     * @return boolean
     */
    final public function add($quantity = 1)
    {
        if (!is_int($quantity) || $quantity < 0) {
            throw new LicenseException("Quantity must be a positive integer.");
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
    final public function sub($quantity = 1)
    {
        if (!is_int($quantity) || $quantity < 0) {
            throw new LicenseException("Quantity must be a positive integer.");
        }

        if ($this->model->quantity - $quantity < 0) {
            throw new LicenseException("You cannot remove more licenses than you have available.");
        }

        $this->subtracting($quantity);

        $this->model->quantity -= $quantity;
        $this->model->save();

        $this->subtracted($quantity);
        
        return true;
    }

    /**
     * Set the amount of licenses
     *
     * @param int $quantity
     * @return boolean
     */
    final public function set($quantity)
    {
        if (!is_int($quantity) || $quantity < 0) {
            throw new LicenseException("Quantity must be a positive integer.");
        }

        $difference = $quantity - $this->maximum();

        if ($difference < 0) {
            return $this->sub(abs($difference));
        } else {
            return $this->add($difference);
        }
    }
}
