<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('sensor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_alat')->constrained('alat')->onDelete('cascade');
            $table->string('nama_sensor');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('sensor');
    }
};

