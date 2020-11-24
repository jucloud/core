<?php

namespace JuCloud\Core\Supports\Traits;

use ReflectionClass;
use JuCloud\Core\Supports\Str;

trait Arrayable
{
    /**
     * toArray.
     *
     * @throws \ReflectionException
     */
    public function toArray(): array
    {
        $result = [];

        foreach ((new ReflectionClass($this))->getProperties() as $item) {
            $k = $item->getName();
            $method = 'get'.Str::studly($k);

            $result[Str::snake($k)] = method_exists($this, $method) ? $this->{$method}() : $this->{$k};
        }

        return $result;
    }
}
