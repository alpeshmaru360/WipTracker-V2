<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = [
        'rolename', 
        'status', 
        'is_redirect', // A Code: 22-12-2025
        'created_at', 
        'updated_at'
    ];    
}
