<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KosController extends Controller
{
    public function index(Request $request)
    {
        $query = Kos::query();

        // Gunakan whereRaw untuk Haversine, yang lebih fleksibel
        if ($request->has(['latitude', 'longitude', 'radius'])) {
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius');

            $haversine = "(
                6371 * acos(
                    cos(radians({$latitude})) * cos(radians(latitude)) * cos(radians(longitude) - radians({$longitude})) +
                    sin(radians({$latitude})) * sin(radians(latitude))
                )
            )";

            $query->selectRaw("kos.*, $haversine AS distance")
                ->having('distance', '<=', $radius)
                ->orderBy('distance');
        }

        // Filter berdasarkan pencarian nama atau lokasi
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('location', 'like', "%$search%");
            });
        }

        // Filter berdasarkan fasilitas
        if ($request->has('facilities') && !empty($request->facilities)) {
            $facilities = explode(',', $request->facilities);
            foreach ($facilities as $facility) {
                $query->where('facilities', 'like', '%"' . trim($facility) . '"%');
            }
        }

        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan harga maksimum
        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        $kosList = $query->with('reviews')->get();

        return response()->json([
            'status' => 'success',
            'data' => $kosList,
        ]);
    }

    public function show(Kos $kos)
    {
        $kos->load('reviews');
        return response()->json($kos);
    }
}