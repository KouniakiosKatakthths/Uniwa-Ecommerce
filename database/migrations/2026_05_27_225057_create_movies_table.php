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
        Schema::create('movies', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string('title');
            $table->text('description');
            $table->text('poster_url');
            $table->text('trailer_url');
            $table->string('rating');
            $table->integer('duration');
            $table->enum('status', ['now_playing', 'coming_soon', 'archived'])->default('now_playing');
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
