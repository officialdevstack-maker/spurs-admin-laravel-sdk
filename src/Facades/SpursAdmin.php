<?php

namespace SpursCloud\Admin\Facades;

use Illuminate\Support\Facades\Facade;
use SpursCloud\Admin\AdminConfig;

/**
 * @method static array all()
 * @method static mixed get(string $key, mixed $default = null)
 * @method static bool has(string $key)
 * @method static array refresh()
 *
 * @see \SpursCloud\Admin\AdminConfig
 */
class SpursAdmin extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AdminConfig::class;
    }
}
