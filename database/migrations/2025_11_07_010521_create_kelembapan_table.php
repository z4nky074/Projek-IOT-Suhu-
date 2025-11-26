<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('kelembapan', function (Blueprint $table) {
            $table->id();
            $table->float('nilai_kelembapan');
            $table->foreignId('id_sensor')->constrained('sensor')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('kelembapan');
    }
};
