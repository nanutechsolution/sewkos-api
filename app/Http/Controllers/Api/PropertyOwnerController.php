<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\RoomType;
use App\Models\RoomTypeImage;
use App\Models\Room;
use App\Models\RoomTypePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // 1. Validasi data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender_preference' => 'required|string|in:Putra,Putri,Campur',
            'description' => 'required|string',
            'rules' => 'nullable|string',
            'year_built' => 'required|integer|min:1900|max:' . date('Y'),
            'manager_name' => 'nullable|string|max:255',
            'manager_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'address_street' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'address_province' => 'required|string|max:255',
            'address_zip_code' => 'nullable|string|max:10',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'property_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'property_image_types.*' => 'nullable|string',
            'rules_file' => 'nullable|file|mimes:pdf|max:2048',
            'general_facilities' => 'nullable|array',
            'general_facilities.*' => 'nullable|integer|exists:facilities,id',

            // Property Images (optional)
            'property_images' => 'nullable|array',
            'property_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'property_image_types' => 'nullable|array',
            'property_image_types.*' => 'string|in:front_view,interior,street_view,other',


            'room_types.*.name' => 'required|string|max:255',
            'room_types.*.description' => 'required|string',
            'room_types.*.size_m2' => 'required|numeric',
            'room_types.*.total_rooms' => 'required|integer|min:1',
            'room_types.*.prices.*.period_type' => 'required|string',
            'room_types.*.prices.*.price' => 'required|numeric|min:0',
            'room_type_images.*.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'room_type_image_types.*.*' => 'nullable|string',
            'room_type_specific_facilities.*' => 'nullable|array',
            'room_type_specific_facilities.*.*' => 'nullable|integer|exists:facilities,id',
            'rooms.*.*.room_number' => 'required|string|max:255',
            'rooms.*.*.floor' => 'required|integer',
            'rooms.*.*.status' => 'required|string|in:available,occupied,maintenance',


            // Room type images
            'room_type_images' => 'nullable|array',
            'room_type_images.*.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'room_type_image_types' => 'nullable|array',
            'room_type_image_types.*.*' => 'string|in:cover,interior,bathroom,other',
        ]);

        try {
            // Gunakan transaksi untuk memastikan semua data tersimpan atau tidak sama sekali
            return DB::transaction(function () use ($validated, $request) {
                // 2. Simpan data properti utama
                $property = Property::create([
                    'user_id' => auth()->id(), // Ambil ID pemilik dari user yang sedang login
                    'name' => $validated['name'],
                    'gender_preference' => $validated['gender_preference'],
                    'description' => $validated['description'],
                    'rules' => $validated['rules'],
                    'year_built' => $validated['year_built'],
                    'manager_name' => $validated['manager_name'] ?? null,
                    'manager_phone' => $validated['manager_phone'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'address_street' => $validated['address_street'],
                    'address_city' => $validated['address_city'],
                    'address_province' => $validated['address_province'],
                    'address_zip_code' => $validated['address_zip_code'],
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                ]);

                // 3. Unggah dan simpan gambar properti
                foreach ($request->file('property_images') as $index => $imageFile) {
                    $path = $imageFile->store('property_images', 'public');
                    $relativePath = str_replace('public/', '', $path);
                    $property->images()->create([
                        'image_url' => $relativePath,
                        'type' => $request->input("property_image_types.$index"),
                    ]);
                }
                // 4. Unggah dan simpan file peraturan
                if ($request->hasFile('rules_file')) {
                    $path = $request->file('rules_file')->store('public/rules_files');
                    $property->update(['rules_file_url' => Storage::url($path)]);
                }

                // 5. Simpan tipe kamar dan data relasinya
                if ($request->has('room_types')) {
                    foreach ($request->input('room_types') as $index => $roomTypeData) {
                        $roomType = $property->roomTypes()->create([
                            'name' => $roomTypeData['name'],
                            'description' => $roomTypeData['description'],
                            'size_m2' => $roomTypeData['size_m2'],
                            'total_rooms' => $roomTypeData['total_rooms'],
                            'available_rooms' => $roomTypeData['total_rooms'],
                        ]);

                        // Simpan harga sewa
                        if ($request->has("room_types.$index.prices")) {
                            foreach ($request->input("room_types.$index.prices") as $priceData) {
                                $roomType->prices()->create($priceData);
                            }
                        }

                        // Simpan fasilitas spesifik kamar
                        if ($request->has("room_type_specific_facilities.$index")) {
                            $roomType->facilities()->sync($request->input("room_type_specific_facilities.$index"));
                        }

                        // Unggah dan simpan gambar tipe kamar
                        if ($request->hasFile("room_type_images.$index")) {
                            foreach ($request->file("room_type_images.$index") as $imgIndex => $imageFile) {
                                // Simpan di storage/app/public/room_type_images
                                $path = $imageFile->store('room_type_images', 'public');
                                // Hapus 'public/' dari path agar tersimpan bersih di DB
                                $relativePath = str_replace('public/', '', $path);
                                $roomType->images()->create([
                                    'image_url' => $relativePath, // path bersih
                                    'type' => $request->input("room_type_image_types.$index.$imgIndex"),
                                ]);
                            }
                        }
                        // Simpan kamar individual
                        if ($request->has("rooms.$index")) {
                            $roomType->rooms()->createMany($request->input("rooms.$index"));
                        }
                    }
                }

                // 6. Simpan fasilitas umum
                if ($request->has('general_facilities')) {
                    $property->facilities()->sync($validated['general_facilities']);
                }

                // Muat ulang relasi agar respons lengkap
                $property->load([
                    'images',
                    'facilities',
                    'roomTypes.prices',
                    'roomTypes.facilities',
                    'roomTypes.images',
                    'roomTypes.rooms'
                ]);

                return response()->json(['message' => 'Properti berhasil dibuat.', 'data' => $property], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal membuat properti.', 'error' => $e->getMessage()], 500);
        }
    }
    public function store1(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

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
            'room_types.*.price_daily' => 'nullable|numeric|min:0',
            // 'room_types.*.price_monthly' => 'required|numeric|min:0',
            'room_types.*.price_3_months' => 'nullable|numeric|min:0',
            'room_types.*.price_6_months' => 'nullable|numeric|min:0',
            'room_types.*.specific_facilities' => 'nullable|array',
            'room_types.*.specific_facilities.*' => 'integer',

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
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_url' => Storage::url($file->store('property_images', 'public')),
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
                foreach (['daily', 'monthly', '3_months', '6_months'] as $period) {
                    $priceKey = 'price_' . $period;
                    if (isset($rtData[$priceKey])) {
                        RoomTypePrice::create([
                            'room_type_id' => $roomType->id,
                            'period_type' => $period,
                            'price' => $rtData[$priceKey],
                        ]);
                    }
                }

                // Room type images
                if ($request->hasFile("room_type_images.$rtIndex")) {
                    foreach ($request->file("room_type_images.$rtIndex") as $imgIndex => $file) {
                        RoomTypeImage::create([
                            'room_type_id' => $roomType->id,
                            'image_url' => Storage::url($file->store('room_type_images', 'public')),
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

    public function update(Request $request, Property $property)
    {
        // if (!$request->user() || $request->user()->id !== $property->user_id) {
        //     return response()->json(['message' => 'Unauthorized.'], 403);
        // }

        // Validasi data properti utama (sebagian besar nullable untuk update parsial)
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'gender_preference' => 'nullable|string|in:Putra,Putri,Campur',
            'description' => 'nullable|string',
            'rules' => 'nullable|string',
            'rules_file' => 'nullable|file|mimes:pdf|max:2048',
            'year_built' => 'nullable|integer|min:1900|max:' . date('Y'),
            'manager_name' => 'nullable|string|max:255',
            'manager_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'address_street' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:255',
            'address_province' => 'nullable|string|max:255',
            'address_zip_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'total_rooms' => 'nullable|integer|min:0',
            'available_rooms' => 'nullable|integer|min:0',

            // Validasi untuk gambar properti (jika ada yang baru diupload)
            'property_images_to_add' => 'nullable|array',
            'property_images_to_add.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'property_image_types_to_add' => 'nullable|array',
            'property_image_types_to_add.*' => 'string|in:front_view,interior,street_view,other',
            'property_images_to_delete' => 'nullable|array',

            // Validasi untuk tipe kamar (JSON string)
            'room_types_to_update' => 'nullable|string',
            'room_types_to_delete' => 'nullable|array',

            // Validasi untuk gambar tipe kamar (nested array of files)
            'room_type_images_to_add' => 'nullable|array',
            'room_type_images.*.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'room_type_image_types' => 'nullable|array',
            'room_type_image_types.*.*' => 'string|in:cover,interior,bathroom,other',

            // Validasi untuk kamar individual (JSON string)
            'rooms_to_update' => 'nullable|string',

            // Validasi untuk fasilitas (JSON string)
            'general_facilities' => 'nullable|string',
            'room_type_specific_facilities_to_update' => 'nullable|string',
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
            $roomTypesToUpdateData = json_decode($request->room_types_to_update ?? '[]', true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON for room_types_to_update.');
            }

            $roomTypesToDeleteIds = json_decode($request->room_types_to_delete ?? '[]', true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON for room_types_to_delete.');
            }

            $propertyImagesToDeleteIds = json_decode($request->property_images_to_delete ?? '[]', true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON for property_images_to_delete.');
            }

            $roomsToUpdateData = json_decode($request->rooms_to_update ?? '[]', true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON for rooms_to_update.');
            }

            $generalFacilities = json_decode($request->general_facilities ?? '[]', true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON for general_facilities.');
            }

            $roomTypeSpecificFacilitiesToUpdate = json_decode($request->room_type_specific_facilities_to_update ?? '[]', true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON for room_type_specific_facilities_to_update.');
            }


            $property->update([
                'name' => $request->name,
                'gender_preference' => $request->gender_preference,
                'description' => $request->description,
                'rules' => $request->rules,
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
                'total_rooms' => $request->total_rooms,
                'available_rooms' => $request->available_rooms,
            ]);

            if ($request->hasFile('rules_file')) {
                if ($property->rules_file_url) {
                    Storage::disk('public')->delete(str_replace(url('/storage'), '', $property->rules_file_url));
                }
                $property->rules_file_url = Storage::url($request->file('rules_file')->store('rules_files', 'public'));
                $property->save();
            }

            if (!empty($propertyImagesToDeleteIds)) {
                foreach ($propertyImagesToDeleteIds as $imageId) {
                    $image = PropertyImage::find($imageId);
                    if ($image && $image->property_id == $property->id) {
                        Storage::disk('public')->delete(str_replace(url('/storage'), '', $image->image_url));
                        $image->delete();
                    }
                }
            }
            if ($request->hasFile('property_images_to_add')) {
                foreach ($request->file('property_images_to_add') as $index => $imageFile) {
                    $imagePath = $imageFile->store('property_images', 'public');
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_url' => Storage::url($imagePath),
                        'type' => $request->property_image_types_to_add[$index] ?? 'other',
                    ]);
                }
            }

            if (!empty($generalFacilities)) {
                $property->facilities()->sync($generalFacilities);
            } else {
                $property->facilities()->detach();
            }

            if (!empty($roomTypesToDeleteIds)) {
                foreach ($roomTypesToDeleteIds as $roomTypeId) {
                    $roomType = RoomType::find($roomTypeId);
                    if ($roomType && $roomType->property_id == $property->id) {
                        $roomType->images()->each(function ($img) {
                            Storage::disk('public')->delete(str_replace(url('/storage'), '', $img->image_url));
                            $img->delete();
                        });
                        $roomType->rooms()->delete();
                        $roomType->facilities()->detach();
                        $roomType->delete();
                    }
                }
            }

            if (!empty($roomTypesToUpdateData)) {
                foreach ($roomTypesToUpdateData as $rtIndex => $rtData) {
                    $roomType = null;
                    if (isset($rtData['id'])) {
                        $roomType = RoomType::find($rtData['id']);
                    }

                    $roomTypeData = [
                        'name' => $rtData['name'],
                        'description' => $rtData['description'] ?? null,
                        'size_m2' => $rtData['size_m2'] ?? null,
                        'total_rooms' => $rtData['total_rooms'],
                        'available_rooms' => $rtData['available_rooms'] ?? $rtData['total_rooms'],
                    ];

                    if ($roomType && $roomType->property_id == $property->id) {
                        $roomType->update($roomTypeData);
                    } else {
                        $roomType = $property->roomTypes()->create($roomTypeData);
                    }

                    // Simpan/update harga di tabel room_type_prices
                    $pricingPeriods = ['daily', 'monthly', '3_months', '6_months'];
                    foreach ($pricingPeriods as $period) {
                        $priceKey = 'price_' . $period;
                        if (isset($rtData[$priceKey]) && $rtData[$priceKey] !== null) {
                            RoomTypePrice::updateOrCreate(
                                ['room_type_id' => $roomType->id, 'period_type' => $period],
                                ['price' => $rtData[$priceKey]]
                            );
                        } else {
                            // Jika harga dihapus dari request, hapus juga dari DB
                            RoomTypePrice::where('room_type_id', $roomType->id)
                                ->where('period_type', $period)
                                ->delete();
                        }
                    }

                    if ($request->hasFile("room_type_images_to_add.$rtIndex")) {
                        foreach ($request->file("room_type_images_to_add.$rtIndex") as $imgIndex => $rtImageFile) {
                            $rtImagePath = $rtImageFile->store('room_type_images', 'public');
                            RoomTypeImage::create([
                                'room_type_id' => $roomType->id,
                                'image_url' => Storage::url($rtImagePath),
                                'type' => $request->room_type_image_types_to_add[$rtIndex][$imgIndex] ?? 'other',
                            ]);
                        }
                    }
                    if (isset($rtData['images_to_delete']) && !empty($rtData['images_to_delete'])) {
                        foreach ($rtData['images_to_delete'] as $rtImageId) {
                            $rtImage = RoomTypeImage::find($rtImageId);
                            if ($rtImage && $rtImage->room_type_id == $roomType->id) {
                                Storage::disk('public')->delete(str_replace(url('/storage'), '', $rtImage->image_url));
                                $rtImage->delete();
                            }
                        }
                    }

                    if (isset($roomTypeSpecificFacilitiesToUpdate[$rtIndex]) && !empty($roomTypeSpecificFacilitiesToUpdate[$rtIndex])) {
                        $roomType->facilities()->sync($roomTypeSpecificFacilitiesToUpdate[$rtIndex]);
                    } else {
                        $roomType->facilities()->detach();
                    }

                    if (isset($rtData['rooms']) && !empty($rtData['rooms'])) {
                        foreach ($rtData['rooms'] as $roomData) {
                            $room = null;
                            if (isset($roomData['id'])) {
                                $room = Room::find($roomData['id']);
                            }
                            $roomIndividualData = [
                                'room_number' => $roomData['room_number'],
                                'floor' => $roomData['floor'] ?? null,
                                'status' => $roomData['status'],
                            ];
                            if ($room && $room->room_type_id == $roomType->id) {
                                $room->update($roomIndividualData);
                            } else {
                                $roomType->rooms()->create($roomIndividualData);
                            }
                        }
                    }
                    if (isset($rtData['rooms_to_delete']) && !empty($rtData['rooms_to_delete'])) {
                        foreach ($rtData['rooms_to_delete'] as $roomId) {
                            $room = Room::find($roomId);
                            if ($room && $room->room_type_id == $roomType->id) {
                                $room->delete();
                            }
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Properti berhasil diperbarui!',
                'data' => $property->load(['images', 'roomTypes.images', 'roomTypes.rooms', 'roomTypes.facilities', 'facilities'])
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui properti.',
                'error_detail' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }


    public function destroy(Property $property)
    {
        if (!Auth::check() || Auth::user()->id !== $property->user_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        DB::beginTransaction();

        try {
            // Hapus gambar properti
            $property->images()->each(function ($image) {
                Storage::disk('public')->delete(str_replace(url('/storage'), '', $image->image_url));
                $image->delete();
            });

            // Hapus gambar tipe kamar dan kamar individual
            $property->roomTypes()->each(function ($roomType) {
                $roomType->images()->each(function ($img) {
                    Storage::disk('public')->delete(str_replace(url('/storage'), '', $img->image->url));
                    $img->delete();
                });
                $roomType->rooms()->delete();
                $roomType->facilities()->detach();
            });

            // Detach fasilitas umum
            $property->facilities()->detach();

            // Hapus properti utama
            $property->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Properti berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus properti.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
