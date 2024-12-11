<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Main extends Model
{
    protected $table = 'main';
    public $timestamps = true;

    protected $fillable = [
        'key',
        'value'
    ];
    
    protected $casts = [
        'value' => 'json',
    ];
}
