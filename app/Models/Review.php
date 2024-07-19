<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable  = ['review', 'rating'];

    // this method inside the review model is used to define an inverse side of the one to many relationship between a review class and book class
    public function book()
    {
        // each review belongs to one book
        return $this->belongsTo(Book::class);
    }

    // booted event to handler cache when there is information updated or deleted or created in database
    // it will work like this when there is data updated or deleted or created in database it will clear the cache to show the new data
    protected static function booted()
    {
        static::updated(fn (Review $review) => cache()->forget('book:' . $review->book_id));
        static::deleted(fn (Review $review) => cache()->forget('book:' . $review->book_id));
        static::created(fn (Review $review) => cache()->forget('book:' . $review->book_id));
    }
}
