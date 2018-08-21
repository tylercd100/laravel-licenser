<?php

namespace Tylercd100\License;

use Tylercd100\License\License;

trait HasLicenses
{
    /**
     * Attempt to allocate unused licenses.
     *
     * @param string $class The License class you want to work with
     * @param int $quantity The amount of licenses you want to attempt to use
     * @param boolean $add If true then it will increase the maximum available licenses
     * @return self
     */
    public function licensesAllocate($class, $quantity, $add = false)
    {
        $license = $this->getLicenseInstance($class);        
        $license->allocate($quantity, $add);
        return $this;
    }

    /**
     * Attempt to Deallocate used licenses.
     *
     * @param string $class The License class you want to work with
     * @param int $quantity The amount of licenses you want to attempt to use
     * @param boolean $add If true then it will increase the maximum available licenses
     * @return self
     */
    public function licensesDeallocate($class, $quantity, $sub = false)
    {
        $license = $this->getLicenseInstance($class);        
        $license->deallocate($quantity, $sub);
        return $this;
    }

    /**
     * Returns the amount of unused licenses.
     *
     * @param string $class The License class you want to work with
     * @return int
     */
    public function licensesRemaining($class)
    {
        $license = $this->getLicenseInstance($class);        
        return $license->remaining();
    }

    /**
     * Returns the amount of used licenses
     *
     * @param string $class The License class you want to work with
     * @return int
     */
    public function licensesUsed($class)
    {
        $license = $this->getLicenseInstance($class);        
        return $license->used();
    }

    /**
     * Increase the maximum amount of licenses
     *
     * @param string $class The License class you want to work with
     * @param integer $quantity
     * @return self
     */
    public function licensesAdd($class, $quantity = 1)
    {
        // Add quantity of licenses
        $license = $this->getLicenseInstance($class);
        $license->add($quantity);
        return $this;
    }

    /**
     * Decreases the maximum amount of licenses available
     *
     * @param string $class The License class you want to work with
     * @param integer $quantity
     * @return self
     */
    public function licensesSub($class, $quantity = 1)
    {
        // Subtract quantity of licenses
        $license = $this->getLicenseInstance($class);
        $license->sub($quantity);
        return $this;
    }

    /**
     * Sets the maximum amount of licenses to a specific value
     *
     * @param string $class The License class you want to work with
     * @param integer $quantity
     * @return self
     */
    public function licensesSet($class, $quantity)
    {
        // Set the quantity of licenses
        $license = $this->getLicenseInstance($class);
        $license->set($quantity);
        return $this;
    }

    /**
     * Creates an instance of License from the supplied classname string
     *
     * @param string $class The License class you want to work with
     * @return License
     */
    private function getLicenseInstance($class)
    {
        $license = new $class($this);
        if (!($license instanceof License)) {
            throw LicenseException("Expected ".get_class($license)." to be an instanceof ".License::class);
        }
        return $license;
    }
}
