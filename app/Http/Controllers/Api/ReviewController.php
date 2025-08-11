<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property; // Ubah dari Kos menjadi Property
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Tambahkan ini untuk mendapatkan user_id

class ReviewController extends Controller
{
    public function store(Request $request, Property $property)
    {
        $validator = Validator::make($request->all(), [
            'author_name' => 'required|string|max:255',
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $review = $property->reviews()->create([
            'user_id' => Auth::check() ? Auth::user()->id : null, // Simpan user_id jika login
            'author_name' => $request->author_name,
            'comment' => $request->comment,
            'rating' => $request->rating,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Ulasan berhasil ditambahkan',
            'data' => $review
        ], 201);
    }
}