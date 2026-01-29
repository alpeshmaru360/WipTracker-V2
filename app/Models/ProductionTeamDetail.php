<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionTeamDetail extends Model
{
    use HasFactory;

    protected $table = 'production_team_details';
    protected $fillable = [
        'profile_pic',
        'name',
        'designation',
        'email',
    ];
}