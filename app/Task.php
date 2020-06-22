<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    protected $fillable = [
        'description', 'priority', 'user_id', 'done'
    ];
}
