<?php

namespace App\Http\Controllers;

use App\Models\Fleet;
use Illuminate\Http\Request;

class FleetController extends Controller
{
     // CREATE FLEET / TRANSPORTER GROUP
    public function storeFleet(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Only fleet managers can create transporter groups
        if ($user->role !== 'fleet_manager') {
            return response()->json([
                'status' => false,
                'message' => 'Only fleet managers can create transporter groups.'
            ], 403);
        }

        $validated = $request->validate([
            'fleet_name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100',
        ]);

        $fleet = Fleet::create([
            'manager_id' => $user->id,
            'fleet_name' => $validated['fleet_name'],
            'description' => $validated['description'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'registration_number' => $validated['registration_number'] ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Transporter group created successfully.',
            'fleet' => $fleet,
        ], 201);
    }


    // READ ALL FLEETS
    public function readAllFleets()
    {
        $fleets = Fleet::with('manager')->get();

        return response()->json([
            'status' => true,
            'fleets' => $fleets,
        ], 200);
    }


    // READ ONE FLEET
    public function readFleet($id)
    {
        $fleet = Fleet::with('manager')
            ->with('members')
            ->find($id);

        if (!$fleet) {
            return response()->json([
                'status' => false,
                'message' => 'Transporter group not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'fleet' => $fleet,
        ], 200);
    }


    // UPDATE FLEET
    public function update(Request $request, $id)
    {
        $fleet = Fleet::findOrFail($id);

        if (!$fleet) {
            return response()->json([
                'status' => false,
                'message' => 'Transporter group not found.'
            ], 404);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Only the fleet manager who owns the group can update it
        if ((int) $fleet->manager_id !== (int) $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to update this transporter group.'
            ], 403);
        }

        $validated = $request->validate([
            'fleet_name' => 'sometimes|string|max:150',
            'description' => 'sometimes|nullable|string',
            'phone_number' => 'sometimes|nullable|string|max:20',
            'email' => 'sometimes|nullable|email|max:150',
            'address' => 'sometimes|nullable|string|max:255',
            'registration_number' => 'sometimes|nullable|string|max:100',
            'status' => 'sometimes|in:active,inactive,suspended',
        ]);

        $fleet->update($validated);

        $fleet->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Transporter group updated successfully.',
            'fleet' => $fleet,
        ], 200);
    }


    // DELETE FLEET
    public function destroy(Request $request, $id)
    {
        $fleet = Fleet::findOrFail($id);

        if (!$fleet) {
            return response()->json([
                'status' => false,
                'message' => 'Transporter group not found.'
            ], 404);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Only the fleet manager who owns the group can delete it
        if ((int) $fleet->manager_id !== (int) $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to delete this transporter group.'
            ], 403);
        }

        $fleet->delete();

        return response()->json([
            'status' => true,
            'message' => 'Transporter group deleted successfully.'
        ], 200);
    }
}
