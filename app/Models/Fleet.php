<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{
      use HasFactory;

    protected $table = 'fleets';


    protected $fillable = [
        'manager_id',
        'fleet_name',
        'description',
        'phone_number',
        'email',
        'address',
        'registration_number',
        'logo',
        'verification_status',
        'status',
        'rating',
        'total_deliveries',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function members()
    {
        return $this->hasMany(FleetMember::class, 'fleet_id');
    }
}
