<?php

namespace Tylercd100\License\Traits;

use Tylercd100\License\Maintainer\Maintainer;

trait HasLicenses
{
    public function checkLicensesAvailable($class, $quantity)
    {
        $maintainer = $this->getMaintainerInstance($class);        
        $maintainer->check($this, $quantity);
        return $this;
    }

    public function getLicensesRemaining($class)
    {
        $maintainer = $this->getMaintainerInstance($class);        
        return $maintainer->remaining($this);
    }

    public function addLicenses($class, $quantity = 1)
    {
        // Add quantity of licenses
        $maintainer = $this->getMaintainerInstance($class);
        return $maintainer->add($quantity);
    }

    public function subLicenses($class, $quantity = 1)
    {
        // Subtract quantity of licenses
        $maintainer = $this->getMaintainerInstance($class);
        return $maintainer->sub($quantity);
    }

    public function setLicenses($class, $quantity = 1)
    {
        // Set the quantity of licenses
        $maintainer = $this->getMaintainerInstance($class);
        return $maintainer->set($quantity);
    }

    public function removeLicenses($class)
    {
        // Remove all licenses of type
        $maintainer = $this->getMaintainerInstance($class);
        return $maintainer->remove();
    }

    private function getMaintainerInstance($class)
    {
        $maintainer = new $class($this);
        if (!($maintainer instanceof Maintainer)) {
            throw LicenseException("Expected ".get_class($maintainer)." to be an instanceof ".Maintainer::class);
        }
        return $maintainer;
    }
}
