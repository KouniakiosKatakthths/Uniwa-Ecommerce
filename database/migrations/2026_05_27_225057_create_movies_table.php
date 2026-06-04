<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\MovieGenre;
use App\Enums\MovieRating;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string('title');
            $table->text('description');
            $table->text('poster_url');
            $table->text('trailer_url');
            $table->enum('rating', array_column(MovieRating::cases(), 'value'));
            $table->integer('duration');
            $table->json('actors')->nullable();
            $table->text('director');
            $table->enum('genre', array_column(MovieGenre::cases(),'value'));
            $table->boolean('featured')->default(false);
            $table->date('release_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
