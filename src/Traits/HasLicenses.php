<?php

namespace Tylercd100\License\Traits;

use Tylercd100\License\License;

trait HasLicenses
{
    public function checkLicensesAvailable($class, $quantity, $add = false)
    {
        $license = $this->getLicenseInstance($class);        
        $license->check($quantity, $add);
        return $this;
    }

    public function getLicensesRemaining($class)
    {
        $license = $this->getLicenseInstance($class);        
        return $license->remaining();
    }

    public function getLicensesUsed($class)
    {
        $license = $this->getLicenseInstance($class);        
        return $license->used();
    }

    public function addLicenses($class, $quantity = 1)
    {
        // Add quantity of licenses
        $license = $this->getLicenseInstance($class);
        return $license->add($quantity);
    }

    public function subLicenses($class, $quantity = 1)
    {
        // Subtract quantity of licenses
        $license = $this->getLicenseInstance($class);
        return $license->sub($quantity);
    }

    public function setLicenses($class, $quantity = 1)
    {
        // Set the quantity of licenses
        $license = $this->getLicenseInstance($class);
        return $license->set($quantity);
    }

    public function removeLicenses($class)
    {
        // Remove all licenses of type
        $license = $this->getLicenseInstance($class);
        return $license->remove();
    }

    private function getLicenseInstance($class)
    {
        $license = new $class($this);
        if (!($license instanceof License)) {
            throw LicenseException("Expected ".get_class($license)." to be an instanceof ".License::class);
        }
        return $license;
    }
}
