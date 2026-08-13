<?php

namespace App\Http\Controllers;

use App\Models\Fleet;
use App\Models\fleetMember;
use App\Models\rider;
use Illuminate\Http\Request;

class FleetMemberController extends Controller
{
     // ADD RIDER TO A FLEET
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Only fleet managers can add riders
        if ($user->role !== 'fleet_manager') {
            return response()->json([
                'status' => false,
                'message' => 'Only fleet managers can add riders to a fleet.'
            ], 403);
        }

        $validated = $request->validate([
            'fleet_id' => 'required|exists:fleets,id',
            'rider_id' => 'required|exists:riders,id',
            'role' => 'sometimes|in:member,leader',
        ]);

        $fleet = Fleet::findOrFail($validated['fleet_id']);

        // Make sure this fleet manager owns the fleet
        if ((int) $fleet->manager_id !== (int) $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to add members to this fleet.'
            ], 403);
        }

        // Check if rider is already in this fleet
        $existingMember = fleetMember::whereEquals('fleet_id', $validated['fleet_id'])
            ->where('rider_id', $validated['rider_id'])
            ->first();

        if ($existingMember) {
            return response()->json([
                'status' => false,
                'message' => 'This rider is already a member of this fleet.'
            ], 409);
        }

        $member = fleetMember::create([
            'fleet_id' => $validated['fleet_id'],
            'rider_id' => $validated['rider_id'],
            'role' => $validated['role'] ?? 'member',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Rider added to fleet successfully.',
            'member' => $member,
        ], 201);
    }


    // READ ALL MEMBERS
    public function index()
    {
        $members = fleetMember::with([
            'rider.user',
            'fleet'
        ])->get();

        return response()->json([
            'status' => true,
            'members' => $members,
        ], 200);
    }


    // READ ONE MEMBER
    public function show($id)
    {
        $member = fleetMember::with([
            'rider.user',
            'fleet'
        ])->find($id);

        if (!$member) {
            return response()->json([
                'status' => false,
                'message' => 'Fleet member not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'member' => $member,
        ], 200);
    }


    // UPDATE MEMBER
    public function update(Request $request, $id)
    {
        $member = fleetMember::findOrFail($id);

        if (!$member) {
            return response()->json([
                'status' => false,
                'message' => 'Fleet member not found.'
            ], 404);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $fleet = Fleet::findOrFail($member->fleet_id);

        if (!$fleet || (int) $fleet->manager_id !== (int) $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to update this fleet member.'
            ], 403);
        }

        $validated = $request->validate([
            'role' => 'sometimes|in:member,leader',
            'status' => 'sometimes|in:pending,active,suspended',
        ]);

        $member->update($validated);

        $member->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Fleet member updated successfully.',
            'member' => $member,
        ], 200);
    }


    // DELETE MEMBER / REMOVE RIDER FROM FLEET
    public function destroy(Request $request, $id)
    {
        $member = fleetMember::findOrFail($id);

        if (!$member) {
            return response()->json([
                'status' => false,
                'message' => 'Fleet member not found.'
            ], 404);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $fleet = Fleet::findOrFail($member->fleet_id);

        if (!$fleet || (int) $fleet->manager_id !== (int) $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to remove this fleet member.'
            ], 403);
        }

        $member->delete();

        return response()->json([
            'status' => true,
            'message' => 'Rider removed from fleet successfully.'
        ], 200);
    }
}
