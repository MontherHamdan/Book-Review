<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // unsignedBigInteger() we put unsignedBigInteger() because we want to connect it with the id in book table and have unsignedBigInteger type0.
            // $table->unsignedBigInteger('book_id');
            $table->text('review');
            $table->unsignedTinyInteger('rating');
            $table->timestamps();

            //eanble foreign key
            // $table->foreign('book_id')->references('id')->on('books')
            //     ->onUpdate('cascade')->onDelete('cascade');

            // new version have this shortcut for enable foreign key you can replace inisial ('book_id') and replace previous eanble foreign key in above with this method
            // inistial book_id column and make it as a foreign key
            $table->foreignId('book_id')->constrained()
                ->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
