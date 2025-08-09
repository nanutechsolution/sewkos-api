<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    public function searchLocation(Request $request)
    {
        $query = $request->input('query');
        Log::info('Received search query: ' . $query);

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'NanuTechSolution/1.0 (email@example.com)'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 5,
            ]);

            $results = $response->json();

            if (!$results || !is_array($results)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data lokasi tidak ditemukan atau response tidak valid.',
                ], 404);
            }

            $places = [];
            foreach ($results as $result) {
                $places[] = [
                    'name' => $result['display_name'],
                    'address' => $result['display_name'],
                    'latitude' => $result['lat'],
                    'longitude' => $result['lon'],
                ];
            }

            return response()->json([
                'status' => 'success',
                'places' => $places,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mencari lokasi.',
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }
}