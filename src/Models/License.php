<?php

namespace Tylercd100\License\Models;

use Illuminate\Database\Eloquent\Model;
use Tylercd100\License\Interfaces\License as ILicense;

class License extends Model implements ILicense
{
    protected $table = "licenses";
}