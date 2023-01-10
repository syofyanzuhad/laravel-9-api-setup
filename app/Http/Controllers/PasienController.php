<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Http\Request;
use App\Http\Resources\PasienResource;

/**
 * @group Pasien
 * APIs for managing Pasien
 */
class PasienController extends Controller
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
        $pasien = $this->queryBuilder(Pasien::class, ['nama']);

        return $this->queryPaginate($pasien, 'Pasien', PasienResource::class);
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
            'no_rm' => 'required|string|unique:pasien,no_rm',
            'nik' => 'required|string|unique:pasien,nik',
            'nama' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama_id' => 'required|exists:agama,id',
            'gologan_darah' => 'required|in:A,B,AB,O',
            'tinggi_badan' => 'required|numeric',
            'berat_badan' => 'required|numeric',
            'alergi' => 'required|string',
            'alamat' => 'required|string',
            'no_telp' => 'required|string',
            'email' => 'required|email',
            'pekerjaan' => 'required|string',
            'status' => 'required|in:menikah,belum_menikah,cerai_hidup,cerai_mati',
            'nama_ayah' => 'required|string',
            'nama_ibu' => 'required|string',
            'nama_suami_istri' => 'required|string',
            'alamat_keluarga' => 'required|string',
            'no_telp_keluarga' => 'required|string',
            'nama_penanggung_jawab' => 'required|string',
            'hubungan_keluarga' => 'required|string',
            'alamat_penanggung_jawab' => 'required|string',
            'no_telp_penanggung_jawab' => 'required|string',
        ]);

        $pasien = Pasien::create($validated);

        return $this->successResponse($pasien, 'Pasien berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pasien  $pasien
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Pasien $pasien)
    {
        return $this->successResponse($pasien, 'Pasien berhasil ditampilkan');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pasien  $pasien
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Pasien $pasien)
    {
        $validated = $request->validate([
            'no_rm' => 'required|string|unique:pasien,no_rm,' . $pasien->id,
            'nik' => 'required|string|unique:pasien,nik,' . $pasien->id,
            'nama' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama_id' => 'required|exists:agama,id',
            'gologan_darah' => 'required|in:A,B,AB,O',
            'tinggi_badan' => 'required|numeric',
            'berat_badan' => 'required|numeric',
            'alergi' => 'required|string',
            'alamat' => 'required|string',
            'no_telp' => 'required|string',
            'email' => 'required|email',
            'pekerjaan' => 'required|string',
            'status' => 'required|in:menikah,belum_menikah,cerai_hidup,cerai_mati',
            'nama_ayah' => 'required|string',
            'nama_ibu' => 'required|string',
            'nama_suami_istri' => 'required|string',
            'alamat_keluarga' => 'required|string',
            'no_telp_keluarga' => 'required|string',
            'nama_penanggung_jawab' => 'required|string',
            'hubungan_keluarga' => 'required|string',
            'alamat_penanggung_jawab' => 'required|string',
            'no_telp_penanggung_jawab' => 'required|string',
        ]);

        $pasien->update($validated);

        return $this->successResponse($pasien, 'Pasien berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pasien  $pasien
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Pasien $pasien)
    {
        $pasien->delete();

        return $this->successResponse($pasien, 'Pasien berhasil dihapus');
    }
}