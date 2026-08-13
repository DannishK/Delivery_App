<?php
 
namespace App\Http\Controllers;
 
use App\Models\Business;
use Illuminate\Http\Request;
 
class BusinessController extends Controller
{
    // CREATE BUSINESS
    public function createBusiness(Request $request)
    {
        $user = $request->user();
 
        // Make sure only business owners can create a business
        if ($user->role !== 'business_owner') {
            return response()->json([
                'status' => false,
                'message' => 'Only business owners can create a business.'
            ], 403);
        }
 
        // Make sure this user doesn't already have a business
        if ($user->business) {
            return response()->json([
                'status' => false,
                'message' => 'You already have a registered business.'
            ], 409);
        }
 
        $validated = $request->validate([
            'business_name' => 'required|string|max:150',
            'business_type' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'business_email' => 'nullable|email|max:150',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);
 
        $business = Business::create([
            'user_id' => $user->id,
            'business_name' => $validated['business_name'],
            'business_type' => $validated['business_type'],
            'phone' => $validated['phone'],
            'email' => $validated['business_email'] ?? null,
            'description' => $validated['description'] ?? null,
            'address' => $validated['address'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);
 
        return response()->json([
            'status' => true,
            'message' => 'Business registered successfully.',
            'business' => $business,
        ], 201);
    }
 
 
    // READ ALL BUSINESSES
    public function readAll()
    {
        $businesses = Business::with('user')->get();
 
        return response()->json([
            'status' => true,
            'businesses' => $businesses,
        ]);
    }
 
 
    // READ ONE BUSINESS
    public function read(int $id)
    {
        $business = Business::with('user')->find($id);
 
        if (!$business) {
            return response()->json([
                'status' => false,
                'message' => 'Business not found.'
            ], 404);
        }
 
        return response()->json([
            'status' => true,
            'business' => $business,
        ]);
    }
 
 
    // UPDATE BUSINESS


   public function update(Request $request, $id)

{

// dd([
//     'id_from_url' => $id,
//     'logged_in_user_id' => $request->user()->id,
//     'request_data' => $request->all(),
// ]);
    $business = Business::findOrFail($id);

    if (!$business) {
        return response()->json([
            'status' => false,
            'message' => 'Business not found.'
        ], 404);
    }

    $user = $request->user();

    if ((int) $business->user_id !== (int) $user->id) {
        return response()->json([
            'status' => false,
            'message' => 'You do not own this business.'
        ], 403);
    }

    $validated = $request->validate([
        'business_name' => 'sometimes|string|max:150',
        'business_type' => 'sometimes|string|max:100',
        'phone' => 'sometimes|string|max:20',
        'business_email' => 'sometimes|nullable|email|max:150',
        'description' => 'sometimes|nullable|string',
        'address' => 'sometimes|string|max:255',
        'latitude' => 'sometimes|nullable|numeric|between:-90,90',
        'longitude' => 'sometimes|nullable|numeric|between:-180,180',
        'status' => 'sometimes|in:active,inactive, suspended',
    ]);

    $business->update($validated);

    $business->refresh();

    return response()->json([
        'status' => true,
        'message' => 'Business updated successfully.',
        'business' => $business,
    ], 200);
}
 
    // DELETE BUSINESS
    public function destroy(Request $request, $id)
{
    $business = Business::findOrFail($id);

    if (!$business) {
        return response()->json([
            'status' => false,
            'message' => 'Business not found.'
        ], 404);
    }

    $user = $request->user();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthenticated.'
        ], 401);
    }

    // Only the business owner can delete it
    if ((int) $business->user_id !== (int) $user->id) {
        return response()->json([
            'status' => false,
            'message' => 'You are not authorized to delete this business.'
        ], 403);
    }

    $business->delete();

    return response()->json([
        'status' => true,
        'message' => 'Business deleted successfully.'
    ], 200);
}
}
 