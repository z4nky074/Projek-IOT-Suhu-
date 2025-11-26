<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('suhu', function (Blueprint $table) {
            $table->id();
            $table->float('nilai_suhu');
            $table->foreignId('id_sensor')->constrained('sensor')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('suhu');
    }
};

