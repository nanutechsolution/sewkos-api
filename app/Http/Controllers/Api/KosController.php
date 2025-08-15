<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property; // Menggunakan model Property
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KosController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::query();

        // Eager load relasi yang dibutuhkan untuk tampilan daftar
        $query->with([
            'images',
            'roomTypes' => function ($q) {
                $q->with(['images', 'rooms', 'prices']);
            },
            'facilities',
            'reviews'
        ]);

        // Filter berdasarkan pencarian nama atau lokasi
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('address_street', 'like', "%$search%")
                    ->orWhere('address_city', 'like', "%$search%");
            });
        }

        // Filter berdasarkan fasilitas
        if ($request->has('facilities') && !empty($request->facilities)) {
            $facilities = explode(',', $request->facilities);
            foreach ($facilities as $facilityName) {
                $query->whereHas('facilities', function ($q) use ($facilityName) {
                    $q->where('name', 'like', '%' . trim($facilityName) . '%');
                });
            }
        }

        // Filter berdasarkan status ketersediaan kamar
        if ($request->has('status') && $request->input('status') == 'kosong') {
            $query->whereHas('roomTypes', function ($q) {
                $q->where('available_rooms', '>', 0);
            });
        }

        // Filter berdasarkan harga maksimum (dari room_type_prices)
        if ($request->has('price_max')) {
            $priceMax = $request->input('price_max');
            $query->whereHas('roomTypes.prices', function ($q) use ($priceMax) { // Query melalui relasi prices
                $q->where('price', '<=', $priceMax);
            });
        }

        // Filter berdasarkan jangkauan (lokasi)
        if ($request->has(['latitude', 'longitude', 'radius'])) {
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius'); // dalam kilometer

            $haversine = "(
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )
            )";

            $query->select(DB::raw("properties.*, $haversine AS distance"), [$latitude, $longitude, $latitude]);
            $query->having('distance', '<=', $radius);
            $query->orderBy('distance');
        } else {
            $query->select('properties.*'); // Pastikan semua kolom properti terpilih jika tidak ada filter lokasi
        }

        // Filter berdasarkan kategori
        if ($request->has('category') && !empty($request->category)) {
            $query->where('gender_preference', $request->category); // Contoh, sesuaikan jika kategori bukan gender
        }

        $propertiesList = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $propertiesList,
        ]);
    }

    public function show(Property $property)
    {
        $property->load([
            'user',
            'images',
            'roomTypes' => function ($q) {
                $q->with(['images', 'rooms', 'facilities', 'prices']);
            },
            'facilities',
            'reviews' => function ($q) {
                $q->with('user');
            }
        ]);
        return response()->json($property);
    }
}