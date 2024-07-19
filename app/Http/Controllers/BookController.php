<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        // when() it take two parameter and (value) and (function) and when (value) not null it will implement the (function)
        // we will use it for search bar

        // $book = Book::when($title, function ($query, $title) {
        //     return $query->title($title);
        // })->get();

        // you can use arrow function (fn) instead
        $books = Book::when(
            $title,
            fn ($query, $title) => $query->title($title)
        );

        // match its not a function its a statement like switch but it return a value
        // its check if the filter there it will additional query scope to $books
        $books = match ($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6months' => $books->popularLast6Months(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6months' => $books->highestRatedLast6Months(),
            default => $books->latest()->withAvgRating()->withReviewsCount(),
        };

        $books = $books->get();

        // instead of get data in the way in the above we will use file cache
        // $cacheKey = 'books:' . $filter . ':' . $title;
        // ttl:time in seconed (how long it should be cached)
        // arrow function (fn) its to get data to being chched
        // $books = cache()->remember(
        //     $cacheKey,
        //     3600,
        //     fn () =>
        //     $books->get()
        // );

        // php artisan route:list to check what the view name should be 
        return view('books.index', ['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {

        // load() to get the realtion query 
        // return view('books.show', ['book' => $book->load(
        //     [
        //         'reviews' => fn ($query) => $query->latest()
        //     ]
        // )]);

        // here we will changing the way of retreving data using cache 
        $cacheKey = 'book:' . $id;

        $book = cache()->remember(
            $cacheKey,
            3600,
            fn () =>
            Book::with(
                [
                    'reviews' => fn ($query) => $query->latest()
                ]
            )->withReviewsCount()->withAvgRating()->findOrFail($id)
        );

        return view('books.show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
