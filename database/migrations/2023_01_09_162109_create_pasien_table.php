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
        Schema::create('pasien', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm')->unique();
            $table->string('no_bpjs')->unique()->nullable();
            $table->string('nik')->unique();
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->string('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->unsignedBigInteger('agama_id');
            $table->enum('gologan_darah', ['A', 'B', 'AB', 'O']);
            $table->integer('tinggi_badan');
            $table->integer('berat_badan');
            $table->string('alergi');
            $table->text('alamat');
            $table->string('no_telp');
            $table->string('email');
            $table->string('pekerjaan');
            $table->enum('status', ['menikah', 'belum menikah', 'cerai hidup', 'cerai mati']);
            $table->string('nama_ayah');
            $table->string('nama_ibu');
            $table->string('nama_suami_istri');
            $table->string('alamat_keluarga');
            $table->string('no_telp_keluarga');
            $table->string('nama_penanggung_jawab');
            $table->string('hubungan_keluarga');
            $table->string('alamat_penanggung_jawab');
            $table->string('no_telp_penanggung_jawab');
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
        Schema::dropIfExists('pasien');
    }
};
