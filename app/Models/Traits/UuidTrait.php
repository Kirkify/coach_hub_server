<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

// UUID Trait
// https://dev.to/wilburpowery/easily-use-uuids-in-laravel-45be
trait UuidTrait {
    protected static function bootUsesUuid()
    {
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}