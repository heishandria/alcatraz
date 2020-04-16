<?php

namespace App\Annotations;

/**
 * RouteResource annotation class.
 *
 * @Annotation
 * @Target("CLASS")
 */
class ApiResource
{
    /**
     * @var string required
     */
    public $resource;
}
