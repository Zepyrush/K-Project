<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ujians', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('durasi_ujian');
            $table->integer('semester')->nullable();
            $table->enum('tipe_ujian', ['uas', 'uts'])->nullable();
            $table->enum('tipe_soal', ['essay', 'pilihan_ganda'])->nullable();
            $table->enum('random_soal', ['1', '0'])->nullable();
            $table->enum('lihat_hasil', ['1', '0'])->nullable();
            $table->foreignId('jadwal_ujian_id')->constrained('jadwal_ujians')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ujians');
    }
};
