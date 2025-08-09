<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $ownerId = $request->user()->id;
        $kosList = Kos::where('user_id', $ownerId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $kosList
        ]);
    }

    public function store(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'facilities' => 'string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|string|in:kosong,terisi',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePath = $request->file('image')->store('kos_images', 'public');
        // Pecah string fasilitas berdasarkan koma, lalu trim spasi
        $facilitiesArray = array_map('trim', explode(',', $request->facilities));

        $kos = Kos::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'location' => $request->location,
            'price' => $request->price,
            'description' => $request->description,
            'facilities' => json_encode($facilitiesArray),
            'image_url' => Storage::url($imagePath),
            'status' => $request->status,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);


        return response()->json([
            'status' => 'success',
            'message' => 'Kos berhasil diunggah',
            'data' => $kos
        ], 201);
    }

    public function update(Request $request, Kos $kos)
    {
        if (!$request->user() || $request->user()->id !== $kos->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'facilities' => 'required|string',
            'status' => 'required|string|in:kosong,terisi',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Jika ada file gambar baru, simpan dan update image_url
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('kos_images', 'public');
            $kos->image_url = Storage::url($imagePath);
        }

        $kos->update([
            'name' => $request->name,
            'location' => $request->location,
            'price' => $request->price,
            'description' => $request->description,
            'facilities' => json_encode(explode(',', $request->facilities)),
            'status' => $request->status,
            'latitude' => $request->latitude,     // tambah ini
            'longitude' => $request->longitude,   // tambah ini
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data kos berhasil diperbarui.',
            'data' => $kos
        ]);
    }



    public function destroy(Kos $kos)
    {
        // Cek apakah pengguna terotentikasi dan merupakan pemilik kos
        if (!Auth::check() || Auth::user()->id !== $kos->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        try {
            // Ambil jalur relatif dari image_url
            $filePath = str_replace('/storage/', '', parse_url($kos->image_url, PHP_URL_PATH));

            // Hapus file dari penyimpanan jika ada
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Hapus data dari database
            $kos->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Kos berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            // Tangani error jika terjadi masalah
            return response()->json([
                'message' => 'Gagal menghapus kos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}