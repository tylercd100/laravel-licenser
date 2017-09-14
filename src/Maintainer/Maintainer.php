<?php

namespace Tylercd100\Licenser\Maintainer;

use Tylercd100\Licenser\Traits\HasLicenses;
use Tylercd100\Licenser\Models\License;

abstract class Maintainer
{
    function __construct(HasLicenses $owner)
    {
        $this->owner = $owner;
        $this->model = License::firstOrCreate([
            "owner_type" => get_class($owner),
            "owner_id" => $owner->id,
            "maintainer" => get_class($this),
        ], [
            "quantity" => 0,
        ]);
    }

    /**
     * Should return the difference between the maximum amount licenses and what you are trying to limit
     *
     * You should return the difference between w
     * 
     * @param int $total The total licenses
     * @return int
     */
    abstract public function remaining($total);

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
            throw new LicenserExeception("Quantity must be a positive integer.");
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