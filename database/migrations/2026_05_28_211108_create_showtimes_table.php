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
        Schema::create('showtimes', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("movie_id");
            $table->string("room");
            $table->dateTime("starts_at");
            $table->decimal("ticket_price");
            $table->integer('total_seats')->default(100);
            $table->integer('available_seats')->default(100);
            $table->timestamps();

            $table->foreign('movie_id')->references('id')->on('movies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showtimes');
    }
};
