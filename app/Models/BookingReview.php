<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingReview extends Model
{
    use HasFactory;

    protected $table = 'booking_reviews';

    protected $fillable = [
        'booking_id',
        'client_id',
        'rating',
        'review_text',
    ];

    public function booking()
    {
        return $this->belongsTo(Pesanan::class, 'booking_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
