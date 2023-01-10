<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use App\Http\Resources\PegawaiResource;

/**
 * @group Pegawai
 * APIs for managing Pegawai
 */
class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @queryParam search string Kata kunci pencarian. Example: Admin
     * @queryParam except integer Id pengecualian. Example: 1
     * @queryParam limit integer Jumlah yang ingin ditampilkan. Example: 1
     * @queryParam per_page integer Jumlah yang ingin ditampilkan per halaman. Example: 1
     * @queryParam sort string Pengurutan berdasarkan kolom. Example: name
     * @queryParam order string Urutkan sesuai. Example: asc
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $pegawai = $this->queryBuilder(Pegawai::class, ['nama']);

        return $this->queryPaginate($pegawai, 'Pegawai', PegawaiResource::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nik' => 'required|string|unique:pegawai,nik',
            'nip' => 'required|string|unique:pegawai,nip',
            'nama' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'tmt' => 'required|date',
            'pangkat' => 'required|string',
            'golongan' => 'required|string',
            'tmt_pangkat' => 'required|date',
            'status_ketenagakerjaan' => 'required|string',
            'jabatan_eselon' => 'required|string',
            'tmt_eselon' => 'required|date',
            'agama' => 'required|string',
            'status_kawin' => 'required|string',
            'status_pegawai' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'pendidikan_jurusan' => 'required|string',
            'pendidikan_lulus' => 'required|date',
            'pendidikan_kampus' => 'required|string',
        ]);

        $pegawai = Pegawai::create($validated);

        return $this->successResponse($pegawai, 'Pegawai berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pegawai  $pegawai
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Pegawai $pegawai)
    {
        return $this->successResponse($pegawai, 'Pegawai berhasil ditampilkan');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pegawai  $pegawai
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nik' => 'required|string|unique:pegawai,nik,' . $pegawai->id,
            'nip' => 'required|string|unique:pegawai,nip,' . $pegawai->id,
            'nama' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'tmt' => 'required|date',
            'pangkat' => 'required|string',
            'golongan' => 'required|string',
            'tmt_pangkat' => 'required|date',
            'status_ketenagakerjaan' => 'required|string',
            'jabatan_eselon' => 'required|string',
            'tmt_eselon' => 'required|date',
            'agama' => 'required|string',
            'status_kawin' => 'required|string',
            'status_pegawai' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'pendidikan_jurusan' => 'required|string',
            'pendidikan_lulus' => 'required|date',
            'pendidikan_kampus' => 'required|string',
        ]);

        $pegawai->update($validated);

        return $this->successResponse($pegawai, 'Pegawai berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pegawai  $pegawai
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();

        return $this->successResponse($pegawai, 'Pegawai berhasil dihapus');
    }
}