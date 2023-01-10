<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai';

    protected $fillable = [
        'user_id',
        'nik',
        'nip',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'tmt',
        'pangkat',
        'golongan',
        'tmt_pangkat',
        'status_ketenagakerjaan',
        'jaban_eselon',
        'tmt_eselon',
        'jenis_kelamin',
        'agama',
        'status_kawin',
        'status_pegawai',
        'pendidikan_terakhir',
        'pendidikan_jurusan',
        'pendidikan_lulus',
        'pendidikan_kampus',
    ];
}
