<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasien';

    protected $fillable = [
        'no_rm',
        'no_bpjs',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama_id',
        'gologan_darah',
        'tinggi_badan',
        'berat_badan',
        'alergi',
        'alamat',
        'no_telp',
        'email',
        'pekerjaan',
        'status',
        'nama_ayah',
        'nama_ibu',
        'nama_suami_istri',
        'alamat_keluarga',
        'no_telp_keluarga',
        'nama_penanggung_jawab',
        'hubungan_keluarga',
        'alamat_penanggung_jawab',
        'no_telp_penanggung_jawab',
    ];
}
