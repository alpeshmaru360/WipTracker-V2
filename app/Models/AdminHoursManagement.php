<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminHoursManagement extends Model
{
    use HasFactory;
    protected $table = 'admin_hours_management';

    protected $fillable = [
        'lable',
        'process_code',
        'product_type',
        'process_name',
        'key',
        'value'
    ];
}
