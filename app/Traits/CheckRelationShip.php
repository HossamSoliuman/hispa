<?php

namespace App\Traits;

trait CheckRelationShip
{
    public function getRelationsMethods()
    {
        return collect(get_class_methods($this))
            ->filter(function ($method) {
                $reflection = new \ReflectionMethod($this, $method);

                return $reflection->class == self::class &&
                    $reflection->getNumberOfParameters() === 0 &&
                    str_starts_with($method, 'get') === false;
            })
            ->toArray();
    }
}
