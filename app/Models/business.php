<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Business extends Model
{
    use HasFactory;

    protected $table = 'businesses';

    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'phone',
        'business_email',
        'description',
        'address',
        'latitude',
        'longitude',
        'logo',
        'verification_status',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}