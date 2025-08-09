<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kos;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store(Request $request, Kos $kos)
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

        $review = $kos->reviews()->create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Ulasan berhasil ditambahkan',
            'data' => $review
        ], 201);
    }
}