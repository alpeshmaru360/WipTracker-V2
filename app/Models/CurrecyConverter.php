<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrecyConverter extends Model
{
    use HasFactory;

    protected $table = 'currecy_converter';

    protected $fillable = [
        '1_AED',
        '1_USD',
        '1_EUR',
    ];
}
