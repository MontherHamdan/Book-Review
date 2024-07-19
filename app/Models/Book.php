<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;



    // this function to tell laravel that we have one to many relationship between book and review 
    public function reviews()
    {
        // hasMany() method inside book class define the one to many relationship between book class and review class 
        return $this->hasMany(Review::class);
    }


    // define a local query scope
    // Local scopes allow you to define common sets of query constraints that you may easily re-use throughout your application
    // so in this scope we defined a query to fetch the records with a specific title
    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('title', 'LIKE', '%' . $title . '%');
    }


    // local query to count the reviews
    public function scopeWithReviewsCount(Builder $query, $from = null, $to = null): Builder
    {
        // fn mean arrow function
        return $query->withCount([
            'reviews' => fn (Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ]);
    }


    // local query to count the Average of rating
    public function scopeWithAvgRating(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withAvg([
            'reviews' => fn (Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ], 'rating');
    }

    // local query for fetch the most popular book (popular mean have the highest reviews)
    public function scopePopular(Builder $query, $from = null, $to = null): Builder
    {
        // fn mean function
        return $query->withReviewsCount()
            ->orderBy('reviews_count', 'desc');
    }


    // local query for fetch the highest rating books
    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withAvgRating()
            ->orderBy('reviews_avg_rating', 'desc');
    }


    // local query for fetch the records had at least $minReviews of reviews 
    public function scopeMinReviews(Builder $query, int $minReviews): Builder
    {
        // we used having() instead of where() cause we deal with aggregation function
        return $query->having('reviews_count', '>=', $minReviews);
    }


    // method to put a filter range 
    public function dateRangeFilter(Builder $query, $from = null, $to = null)
    {
        if ($from && !$to) {
            $query->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }


    // method to get most popular last month
    public function scopePopularLastMonth(Builder $query): Builder
    {
        return $query->popular(now()->subMonth(), now())
            ->highestRated(now()->subMonth(), now())
            ->minReviews(2);
    }


    // method to get most popular last month
    public function scopePopularLast6Months(Builder $query): Builder
    {
        return $query->popular(now()->subMonths(6), now())
            ->highestRated(now()->subMonths(6), now())
            ->minReviews(5);
    }


    // method to get highest rated last month
    public function scopeHighestRatedLastMonth(Builder $query): Builder
    {
        // it is like scopePopularLastMonth() but we put the highestRated() first because the both of highestRated() and popular() have orderBy() method
        // but the first query has orderBy() it will be work and will ignore the seconed orderBy()
        return $query->highestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviews(2);
    }


    // method to get highest rated last month
    public function scopeHighestRatedLast6Months(Builder $query): Builder
    {
        // it is like scopePopularLast6Month() but we put the highestRated() first because the both of highestRated() and popular() have orderBy() method
        // but the first query has orderBy() it will be work and will ignore the seconed orderBy()
        return $query->highestRated(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReviews(5);
    }

    // booted event to handler cache when there is information updated or deleted in database
    // it will work like this when there is data updated or deleted in database it will clear the cache
    protected static function booted()
    {
        static::updated(fn (Book $book) => cache()->forget('book:' . $book->id));
        static::deleted(fn (Book $book) => cache()->forget('book:' . $book->id));
    }
}
