<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $table = 'admin_setting';
    protected $fillable = ['label', 'key', 'value', 'action', 'reminder_frequency'];
}
