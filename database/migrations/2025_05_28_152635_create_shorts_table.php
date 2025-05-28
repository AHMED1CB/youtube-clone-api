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
        Schema::create('shorts', function (Blueprint $table) {
            
            $table->id();
            
            $table->string('title');
            
            $table->text('descreption');
            
            $table->string('cover')->nullable();

            $table->string('video'); // Short Video Path
            
            $table->string('duration');
            
            $table->string('slug');
            
            $table->foreignId('channel')->constrained('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shorts');
    }
};
