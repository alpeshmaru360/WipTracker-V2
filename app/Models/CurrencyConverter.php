<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyConverter extends Model
{
    use HasFactory;

    // Define the table name explicitly if it's different from the default plural form
    protected $table = 'currecy_converter';

    // Define the fillable columns
    protected $fillable = ['1_AED', '1_USD', '1_EUR'];
}
