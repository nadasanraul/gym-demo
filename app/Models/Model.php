<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class Model extends Eloquent
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->makeHidden([
            'created_at',
            'updated_at',
            'deleted_at',
        ]);
    }
}
