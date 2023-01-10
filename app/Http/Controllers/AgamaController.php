<?php

namespace App\Http\Controllers;

use App\Models\Agama;
use Illuminate\Http\Request;
use App\Http\Resources\AgamaResource;

/**
 * @group Agama
 * APIs for managing Agama
 */
class AgamaController extends Controller
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
        $agama = $this->queryBuilder(Agama::class, ['nama']);

        return $this->queryPaginate($agama, 'Agama', AgamaResource::class);
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
            'nama' => 'required|string',
        ]);

        $agama = Agama::create($validated);

        return $this->successResponse($agama, 'Agama berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Agama  $agama
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Agama $agama)
    {
        return $this->successResponse($agama, 'Agama berhasil ditampilkan');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Agama  $agama
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Agama $agama)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
        ]);

        $agama->update($validated);

        return $this->successResponse($agama, 'Agama berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Agama  $agama
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Agama $agama)
    {
        $agama->delete();

        return $this->successResponse($agama, 'Agama berhasil dihapus');
    }
}