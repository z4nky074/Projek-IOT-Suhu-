<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void{
        Schema::create('batasan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sensor');
            $table->foreign('id_sensor')->references('id')->on('sensor')->onDelete('cascade');
            $table->float('suhu_min');
            $table->float('suhu_max');
            $table->float('kelembapan_min');
            $table->float('kelembapan_max');
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batasan');
    }
};