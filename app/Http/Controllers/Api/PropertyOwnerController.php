<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\RoomTypeImage;
use App\Models\RoomTypePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyOwnerController extends Controller
{

    public function index(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $ownerId = $request->user()->id;
        $propertiesList = Property::where('user_id', $ownerId)
            ->with([
                'images',
                'roomTypes' => function ($q) {
                    $q->with(['images', 'rooms', 'prices']);
                },
                'facilities'
            ])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $propertiesList
        ]);
    }
    public function store(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        Log::debug('Request Input:', $request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'gender_preference' => 'required|string|in:Putra,Putri,Campur',
            'description' => 'required|string',
            'rules' => 'nullable|string',
            'rules_file' => 'nullable|file|mimes:pdf|max:2048',
            'year_built' => 'nullable|integer|min:1900|max:' . date('Y'),
            'manager_name' => 'nullable|string|max:255',
            'manager_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'address_street' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'address_province' => 'required|string|max:255',
            'address_zip_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',

            // Property Images (optional)
            'property_images' => 'nullable|array',
            'property_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'property_image_types' => 'nullable|array',
            'property_image_types.*' => 'string|in:front_view,interior,street_view,other',

            // Room Types
            'room_types' => 'required|array|min:1',
            'room_types.*.name' => 'required|string',
            'room_types.*.description' => 'nullable|string',
            'room_types.*.size_m2' => 'nullable|numeric',
            'room_types.*.total_rooms' => 'required|integer|min:0',
            'room_types.*.prices' => 'required|array',
            'room_types.*.prices.*.period_type' => 'required|string',
            'room_types.*.prices.*.price' => 'required|numeric',

            // Room type images
            'room_type_images' => 'nullable|array',
            'room_type_images.*.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'room_type_image_types' => 'nullable|array',
            'room_type_image_types.*.*' => 'string|in:cover,interior,bathroom,other',

            // Rooms inside room types
            'rooms' => 'nullable|array',
            'rooms.*.*.room_number' => 'required|string',
            'rooms.*.*.floor' => 'nullable|integer',
            'rooms.*.*.status' => 'required|string|in:available,occupied',

            // General facilities
            'general_facilities' => 'nullable|array',
            'general_facilities.*' => 'integer',

            // Room type specific facilities
            'room_type_specific_facilities' => 'nullable|array',
            'room_type_specific_facilities.*' => 'nullable|array',
            'room_type_specific_facilities.*.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Create main property
            $property = Property::create([
                'user_id' => $request->user()->id,
                'name' => $request->name,
                'gender_preference' => $request->gender_preference,
                'description' => $request->description,
                'rules' => $request->rules,
                'rules_file_url' => $request->hasFile('rules_file') ? Storage::url($request->file('rules_file')->store('rules_files', 'public')) : null,
                'year_built' => $request->year_built,
                'manager_name' => $request->manager_name,
                'manager_phone' => $request->manager_phone,
                'notes' => $request->notes,
                'address_street' => $request->address_street,
                'address_city' => $request->address_city,
                'address_province' => $request->address_province,
                'address_zip_code' => $request->address_zip_code,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            // 2. Save property images
            if ($request->hasFile('property_images')) {
                foreach ($request->file('property_images') as $i => $file) {
                    $path = $file->store('property_images', 'public');
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_url' => $path,
                        'type' => $request->property_image_types[$i] ?? 'other',
                    ]);
                }
            }
            // 3. Attach general facilities
            if (!empty($request->general_facilities)) {
                $property->facilities()->attach($request->general_facilities);
            }

            // 4. Room Types + Images + Rooms + Specific Facilities
            $totalRoomsCount = 0;
            $availableRoomsCount = 0;

            foreach ($request->room_types as $rtIndex => $rtData) {
                $roomType = $property->roomTypes()->create([
                    'name' => $rtData['name'],
                    'description' => $rtData['description'] ?? null,
                    'size_m2' => $rtData['size_m2'] ?? null,
                    'total_rooms' => $rtData['total_rooms'],
                    'available_rooms' => $rtData['total_rooms'],
                ]);
                $totalRoomsCount += $rtData['total_rooms'];
                $availableRoomsCount += $rtData['total_rooms'];

                // Prices
                if (!empty($rtData['prices'])) {
                    foreach ($rtData['prices'] as $priceData) {
                        RoomTypePrice::create([
                            'room_type_id' => $roomType->id,
                            'period_type' => $priceData['period_type'],
                            'price' => (float) $priceData['price'],
                        ]);
                    }
                }


                // Room type images
                if ($request->hasFile("room_type_images.$rtIndex")) {
                    foreach ($request->file("room_type_images.$rtIndex") as $imgIndex => $file) {
                        $path = $file->store('room_type_images', 'public');
                        RoomTypeImage::create([
                            'room_type_id' => $roomType->id,
                            'image_url' => $path,
                            'type' => $request->room_type_image_types[$rtIndex][$imgIndex] ?? 'other',
                        ]);
                    }
                }

                // Specific facilities
                if (!empty($request->room_type_specific_facilities[$rtIndex])) {
                    $roomType->facilities()->attach($request->room_type_specific_facilities[$rtIndex]);
                }

                // Rooms
                if (!empty($request->rooms[$rtIndex])) {
                    $currentAvailable = 0;
                    foreach ($request->rooms[$rtIndex] as $room) {
                        $roomType->rooms()->create([
                            'room_number' => $room['room_number'],
                            'floor' => $room['floor'] ?? null,
                            'status' => $room['status'],
                        ]);
                        if ($room['status'] === 'available') {
                            $currentAvailable++;
                        }
                    }
                    $roomType->update(['available_rooms' => $currentAvailable]);
                    $availableRoomsCount -= ($rtData['total_rooms'] - $currentAvailable);
                }
            }

            // Update property room counts
            $property->update([
                'total_rooms' => $totalRoomsCount,
                'available_rooms' => $availableRoomsCount,
            ]);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Properti berhasil dibuat',
                'data' => $property->load(['images', 'roomTypes.images', 'roomTypes.rooms', 'roomTypes.facilities', 'facilities'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat properti',
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified property in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Property  $property
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Property $property)
    {
        // Debug semua input & file
        Log::debug('Request Input:', $request->all());
        // 1. Validasi data
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'gender_preference' => 'sometimes|required|string|in:Putra,Putri,Campur',
            'description' => 'sometimes|required|string',
            'rules' => 'nullable|string',
            'year_built' => 'sometimes|required|integer|min:1900|max:' . date('Y'),
            'address_street' => 'sometimes|required|string|max:255',
            'address_city' => 'sometimes|required|string|max:255',
            'address_province' => 'sometimes|required|string|max:255',
            'address_zip_code' => 'nullable|string|max:10',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric',

            // Property Images
            'property_images_to_delete' => 'nullable|array',
            'property_images_to_delete.*' => 'integer|exists:property_images,id',
            'property_images_to_add.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'property_image_types_to_add.*' => 'string|nullable',


            // Room Types
            'room_types_to_delete' => 'nullable|array',
            'room_types_to_delete.*' => 'integer|exists:room_types,id',
            'room_types_to_update' => 'nullable|array',
            'room_types_to_update.*.id' => 'nullable|integer|exists:room_types,id',
            'room_types_to_update.*.name' => 'sometimes|required|string|max:255',
            'room_types_to_update.*.description' => 'sometimes|required|string',
            'room_types_to_update.*.size_m2' => 'sometimes|required|numeric',
            'room_types_to_update.*.total_rooms' => 'sometimes|required|integer|min:1',
            'room_types_to_update.*.prices' => 'nullable|array',
            'room_types_to_update.*.prices.*.period_type' => 'sometimes|required|string',
            'room_types_to_update.*.prices.*.price' => 'sometimes|required|numeric|min:0',
            'room_types_to_update' => 'required|array',
            'room_types_to_update.*.specific_facilities' => 'nullable|array',
            'room_types_to_update.*.specific_facilities.*' => 'integer|exists:facilities,id',
            // Room Type Images
            'room_types_to_update.*.images_to_add' => 'array',
            'room_types_to_update.*.images_to_add.*' => 'file|image|mimes:jpeg,png,jpg,gif|max:5120',
            'room_types_to_update.*.image_types_to_add' => 'array',
            'room_types_to_update.*.image_types_to_add.*' => 'string|in:front_view,interior,street_view,other,cover,bathroom',

            // Rooms update
            'rooms_to_update.*.*.id' => 'nullable|integer|exists:rooms,id',
            'rooms_to_update.*.*.room_number' => 'sometimes|required|string|max:255',
            'rooms_to_update.*.*.floor' => 'sometimes|required|integer',
            'rooms_to_update.*.*.status' => 'sometimes|required|string|in:available,occupied,maintenance',

            // General facilities
            'general_facilities' => 'nullable|array',
            'general_facilities.*' => 'nullable|integer|exists:facilities,id',
        ]);

        try {
            return DB::transaction(function () use ($validated, $request, $property) {
                // 1. Update data properti utama
                $property->update($validated);
                // 2. Proses gambar properti
                if ($request->filled('property_images_to_delete')) {
                    $imageIds = array_unique($request->property_images_to_delete);
                    foreach ($imageIds as $imageId) {
                        $image = $property->images()->find($imageId);
                        if ($image) {
                            Storage::disk('public')->delete($image->image_url);
                            $image->delete();
                        }
                    }
                }

                if ($request->hasFile('property_images_to_add')) {
                    foreach ($request->file('property_images_to_add') as $i => $file) {
                        $path = $file->store('property_images', 'public');
                        PropertyImage::create([
                            'property_id' => $property->id,
                            'image_url' => $path,
                            'type' => $request->property_image_types_to_add[$i] ?? 'other',
                        ]);
                    }
                }

                // 3. Proses fasilitas umum
                if (isset($validated['general_facilities'])) {
                    $property->facilities()->sync($validated['general_facilities']);
                }

                // 4. Hapus tipe kamar jika ada
                if (isset($validated['room_types_to_delete'])) {
                    $property->roomTypes()->whereIn('id', $validated['room_types_to_delete'])->delete();
                }

                // 5. Update atau buat tipe kamar baru
                if ($request->filled('room_types_to_update')) {
                    foreach ($request->room_types_to_update as $rtIndex => $rtData) {
                        $roomType = $property->roomTypes()->updateOrCreate(
                            ['id' => $rtData['id'] ?? null],
                            $rtData
                        );

                        // Hapus kamar jika ada
                        if (isset($rtData['rooms_to_delete']) && is_array($rtData['rooms_to_delete'])) {
                            $roomType->rooms()->whereIn('id', $rtData['rooms_to_delete'])->delete();
                        }

                        // Update atau buat kamar
                        if (isset($rtData['rooms_to_update'])) {
                            foreach ($rtData['rooms_to_update'] as $roomData) {
                                $roomType->rooms()->updateOrCreate(
                                    ['id' => $roomData['id'] ?? null],
                                    $roomData
                                );
                            }
                        }

                        // Upsert harga sewa
                        if (isset($rtData['prices'])) {
                            $pricesToUpsert = [];
                            foreach ($rtData['prices'] as $priceData) {
                                $pricesToUpsert[] = array_merge($priceData, ['room_type_id' => $roomType->id]);
                            }
                            RoomTypePrice::upsert(
                                $pricesToUpsert,
                                ['id'],
                                ['period_type', 'price']
                            );
                        }

                        // Hapus gambar tipe kamar sesuai $rtIndex
                        $imagesToDelete = $request->input("room_type_images_to_delete.$rtIndex", []);
                        foreach ($imagesToDelete as $imageId) {
                            $image = RoomTypeImage::find($imageId);
                            if ($image) {
                                Storage::disk('public')->delete($image->image_url);
                                $image->delete();
                            }
                        }

                        // Tambah gambar tipe kamar baru sesuai $rtIndex
                        if (isset($rtData['images_to_add'])) {
                            foreach ($rtData['images_to_add'] as $index => $image) {
                                $type = $rtData['image_types_to_add'][$index] ?? 'other';
                                if ($image instanceof \Illuminate\Http\UploadedFile) {
                                    $path = $image->store('room_type_images', 'public');
                                    RoomTypeImage::create([
                                        'room_type_id' => $roomType->id,
                                        'image_url' => $path,
                                        'type' => $type
                                    ]);
                                }
                            }
                        }
                        if (!empty($rtData['specific_facilities'])) {
                            $roomType->facilities()->sync($rtData['specific_facilities']);
                        }
                    }
                }

                // Reload relasi agar respons lengkap
                $property->load([
                    'images',
                    'facilities',
                    'roomTypes.prices',
                    'roomTypes.facilities',
                    'roomTypes.images',
                    'roomTypes.rooms'
                ]);

                return response()->json([
                    'message' => 'Properti berhasil diupdate.',
                    'data' => $property
                ], 200);
            });
        } catch (\Exception $e) {
            Log::error('Update Property Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Gagal mengupdate properti.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Hapus properti beserta semua relasinya.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    function destroy($id)
    {
        try {
            $property = Property::findOrFail($id);

            // Hapus semua file gambar terkait properti
            $property->images->each(function ($image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $image->image_url));
            });

            // Hapus properti itu sendiri dan semua relasinya
            // Jika Anda sudah mengatur onDelete('cascade') di database, ini akan menghapus semua data terkait secara otomatis.
            $property->delete();

            return response()->json(['message' => 'Properti berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus properti.', 'error' => $e->getMessage()], 500);
        }
    }
}