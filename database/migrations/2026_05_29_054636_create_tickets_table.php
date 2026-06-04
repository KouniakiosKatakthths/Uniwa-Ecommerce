<?php

use App\Enums\TicketStatus;
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
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('showtime_id');
            $table->string('seat');
            $table->decimal('price',8,2);
            $table->enum('status', array_column(TicketStatus::cases(), 'value'))->default(TicketStatus::Pending);
            $table->string('qr_code')->nullable();
            $table->string('barcode');
            $table->timestamps();

            //Enforce one ticket per show
            $table->unique(['showtime_id', 'seat']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('showtime_id')->references('id')->on('showtimes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
