<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blood_type',
        'location',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke user (pembuat permintaan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
