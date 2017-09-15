<?php

namespace Tylercd100\License\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $guarded = ["id"];
    protected $table = "licenses";
}