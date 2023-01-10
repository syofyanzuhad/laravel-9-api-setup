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
        Schema::create('pegawai_dummy', function (Blueprint $table) {
            $table->id();
            $table->foreign('user_id')->references('id')->on('users');
            $table->bigInteger('nik')->unique();
            $table->bigInteger('nip')->unique();
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->date('tmt');
            $table->string('pangkat');
            $table->string('golongan');
            $table->date('tmt_pangkat');
            $table->string('status_ketenagakerjaan');
            $table->string('jabatan_eselon');
            $table->date('tmt_eselon');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('agama');
            $table->string('status_kawin');
            $table->string('status_pegawai');
            $table->string('pendidikan_terakhir');
            $table->string('pendidikan_jurusan');
            $table->string('pendidikan_lulus');
            $table->string('pendidikan_kampus');
            // jk
            // jbtn
            // jnj_jabatan
            // kode_kelompok
            // kode_resiko
            // kode_emergency
            // departemen
            // bidang
            // stts_wp
            // stts_kerja
            // npwp
            // pendidikan
            // gapok
            // tmp_lahir
            // tgl_lahir
            // alamat
            // kota
            // mulai_kerja
            // ms_kerja
            // indexins
            // bpd
            // rekening
            // stts_aktif
            // wajibmasuk
            // pengurang
            // indek
            // mulai_kontrak
            // cuti_diambil
            // dankes
            // photo
            // no_ktp
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
        Schema::dropIfExists('pegawai');
    }
};
