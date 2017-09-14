<?php

namespace Tylercd100\Licenser\Models;

use Tylercd100\Licenser\Interfaces\License as ILicense;

class License extends Model implements ILicense
{
    protected $table = "licenses";
}