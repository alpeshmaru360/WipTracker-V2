<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitialInspectionName extends Model
{
    use HasFactory;

    protected $table = 'initial_inspection_name';

    protected $fillable = ['name'];
}
