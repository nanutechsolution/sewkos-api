<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Facility; // Pastikan model Facility sudah dibuat
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::all();
        return response()->json([
            'status' => 'success',
            'data' => $facilities
        ]);
    }
}