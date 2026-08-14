<?php

namespace App\Http\Controllers;

use App\Models\Rider;
use Illuminate\Http\Request;

class RiderController extends Controller
{
      // CREATE RIDER PROFILE
    public function createRider(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if ($user->role !== 'rider') {
            return response()->json([
                'status' => false,
                'message' => 'Only users with the rider role can create a rider profile.'
            ], 403);
        }

        // Prevent duplicate rider profiles
       $existingRider = Rider::query()->where('user_id', $user->id)->exists();

        if ($existingRider) {
             return response()->json([
                'status' => false,
                'message' => 'You already have a rider profile.'
        ], 409);
}

        $validated = $request->validate([
            'id_number' => 'required|string|max:50',
            'vehicle_type' => 'required|string|max:50',
            'vehicle_registration_number' => 'required|string|max:50',
            'license_number' => 'required|string|max:100',
            'license_expiry' => 'required|date',
        ]);

        $rider = Rider::create([
            'user_id' => $user->id,
            'id_number' => $validated['id_number'],
            'vehicle_type' => $validated['vehicle_type'],
            'vehicle_registration_number' => $validated['vehicle_registration_number'],
            'license_number' => $validated['license_number'],
            'license_expiry' => $validated['license_expiry'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Rider profile created successfully.',
            'rider' => $rider,
        ], 201);
    }


    // READ ALL RIDERS
    public function readAllRiders()
    {
        $riders = Rider::with('user')->get();

        return response()->json([
            'status' => true,
            'riders' => $riders,
        ], 200);
    }


    // READ ONE RIDER
    public function readRider($id)
    {
        $rider = Rider::with('user')
            ->with('groups')
            ->find($id);

        if (!$rider) {
            return response()->json([
                'status' => false,
                'message' => 'Rider not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'rider' => $rider,
        ], 200);
    }


    // UPDATE RIDER
    public function update(Request $request,$id)
    {
        $rider = Rider::findorFail($id);

        if (!$rider) {
            return response()->json([
                'status' => false,
                'message' => 'Rider not found.'
            ], 404);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Rider can update their own profile
        if ((int) $rider->user_id !== (int) $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to update this rider.'
            ], 403);
        }

        $validated = $request->validate([
            'id_number' => 'sometimes|string|max:50',
            'vehicle_type' => 'sometimes|string|max:50',
            'vehicle_registration_number' => 'sometimes|string|max:50',
            'license_number' => 'sometimes|string|max:100',
            'license_expiry' => 'sometimes|date',
            'availability_status' => 'sometimes|in:available,busy,offline',
            'latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'longitude' => 'sometimes|nullable|numeric|between:-180,180',
        ]);

        $rider->update($validated);

        $rider->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Rider updated successfully.',
            'rider' => $rider,
        ], 200);
    }


    // DELETE RIDER
    public function destroy(Request $request, $id)
    {
        $rider = Rider::findOrFail($id);

        if (!$rider) {
            return response()->json([
                'status' => false,
                'message' => 'Rider not found.'
            ], 404);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if ((int) $rider->user_id !== (int) $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to delete this rider.'
            ], 403);
        }

        $rider->delete();

        return response()->json([
            'status' => true,
            'message' => 'Rider deleted successfully.'
        ], 200);
    }
}
