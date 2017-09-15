<?php

namespace Tylercd100\License\Models;

use Tylercd100\License\Interfaces\License as ILicense;

class License extends Model implements ILicense
{
    protected $table = "licenses";
}