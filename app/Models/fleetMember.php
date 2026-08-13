<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fleetMember extends Model
{
     use HasFactory;

    protected $table = 'fleet_members';

    protected $fillable = [
        'fleet_id',
        'rider_id',
        'role',
        'status',
        'joined_at',
    ];

    public function fleet()
    {
        return $this->belongsTo(
            Fleet::class,
            'fleet_id'
        );
    }

    public function rider()
    {
        return $this->belongsTo(
            Rider::class,
            'rider_id'
        );
    }
}
