<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
     use HasFactory;

    protected $table = 'riders';

    protected $fillable = [
        'user_id',
        'id_number',
        'profile_photo',
        'vehicle_type',
        'vehicle_registration_number',
        'license_number',
        'license_expiry',
        'availability_status',
        'verification_status',
        'rating',
        'total_deliveries',
        'latitude',
        'longitude',
        'last_location_update',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groups()
    {
        return $this->belongsToMany(
            Fleet::class,
            'fleet_members',
            'rider_id',
            'fleet_id'
        )->withPivot('role', 'status', 'joined_at');
    }
}
