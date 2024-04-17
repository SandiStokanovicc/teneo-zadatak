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
        Schema::create('calendar_archives', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('date');
            $table->tinyInteger('archive_number');
            $table->unsignedBigInteger('absence_id')->nullable();
            $table->foreign('absence_id')->references('id')->on('absences');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_archives');
    }
};
