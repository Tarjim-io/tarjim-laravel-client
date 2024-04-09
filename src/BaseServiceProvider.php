<?php

namespace Tarjim\Laravel;

use Illuminate\Support\ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider
{
    /**
     * Abstract type to bind Tarjim as in the Service Container.
     *
     * @var string
     */
    public static $abstract = 'tarjim';
}
