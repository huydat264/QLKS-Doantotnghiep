<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelFactory
{
    public static function register()
    {
        // This class exists to centralize eager-loading of related models and
        // to avoid `class not found` runtime issues when models are referenced
        // across controllers and services before the autoloader is warmed.
    }
}
