<?php

namespace App\Http\Controllers;

use App\Models\gelleryModel;
use Exception;
// use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class gelleryController extends Controller
{

    public function index($package_id)
    {
        try {
            $images = gelleryModel::where('package_id', $package_id)->get();

            return response()->json([
                'message' => 'Images fetched successfully',
                'data' => $images
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch images',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * POST: Set / Store multiple images
     */

    public function store(Request $request, $package_id)
    {
        try {
            $request->validate([
                'image' => 'required|array',
                // 'image.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB per image
                'image.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB per image
            ]);

            $files = $request->file('image');
            $images = [];

            foreach ($files as $file) {
                $id = uniqid() . '_' . time();
                $filename = Str::uuid() . '_' . $file->getClientOriginalName();

                // Store in 'public/gallery', accessible via 'storage/gallery/...'
                $file->storeAs('gallery', $filename, 'public');

                $images[] = [
                    'id' => $id,
                    'url' => 'gallery/' . $filename,
                ];
            }

            $gallery = gelleryModel::create([
                'images' => $images,
                'package_id' => $package_id,
            ]);

            return response()->json([
                'message' => 'Images uploaded successfully',
                'gallery' => $gallery
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Upload failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function replaceGalleryImages(Request $request, $package_id)
    {
        try {
            // 1. Validate new image uploads
            $validate =  $request->validate([
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'deleted_images' => 'nullable|array'
            ]);

            $newFiles = $request->file('images') ?? [];
            $deleted_images = $request->input('deleted_images') ?? [];
            $newImages = [];

            // 2. Get existing gallery
            $gallery = gelleryModel::where('package_id', $package_id)->first();

            if (!$gallery) {
                return response()->json(['message' => 'Gallery not found'], 404);
            }

            if (!empty($deleted_images)) {
                $this->deleteImagesById($gallery, $deleted_images);
            }

            // 4. Upload and store new images
            foreach ($newFiles as $file) {
                $id = uniqid() . '_' . time();
                $filename = Str::uuid() . '_' . $file->getClientOriginalName();

                $file->storeAs('gallery', $filename, 'public');

                array_push($newImages, [
                    'id' => $id,
                    'url' => 'gallery/' . $filename,
                ]);
            }

            // 5. Update gallery
            $currentImages = $gallery->images ?? [];
            $mergedImages = array_merge($currentImages, $newImages);
            $gallery->images = $mergedImages;

            $gallery->save();

            return response()->json([
                'message' => 'Gallery images replaced successfully',
                'gallery' => $gallery
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Image replacement failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteImageByPackageId($package_id, $image_id)
    {
        // 1. Find the gallery by package_id
        $gallery = gelleryModel::where('package_id', $package_id)->first();

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found'], 404);
        }

        // 2. Get existing images
        $images = $gallery->images ?? [];

        // 3. Find the image to delete
        $imageToDelete = collect($images)->firstWhere('id', $image_id);

        if (!$imageToDelete) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        // 4. Delete the file from storage (optional, but recommended)
        $relativePath = str_replace('storage/', '', $imageToDelete['url']);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }

        // 5. Remove the image from the array
        $updatedImages = array_values(array_filter($images, function ($img) use ($image_id) {
            return $img['id'] !== $image_id;
        }));

        // 6. Update the DB
        $gallery->images = $updatedImages;
        $gallery->save();

        return response()->json(['message' => 'Image deleted successfully']);
    }
    /**
     * Delete: Delete paticular images to the same package
     */

    public function deleteImagesById($gallery, array $ids)
    {
        // Get current images
        $currentImages = $gallery->images ?? [];

        // Filter out images to delete
        $remainingImages = [];

        foreach ($currentImages as $img) {
            // Check if this image should be deleted
            if (in_array($img['id'], $ids)) {
                // Delete file from storage
                $relativePath = str_replace('storage/', '', $img['url']);

                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
            } else {
                // Keep this image
                $remainingImages[] = $img;
            }
        }

        // Update gallery with remaining images
        $gallery->images = $remainingImages;
        $gallery->save();
    }
}
