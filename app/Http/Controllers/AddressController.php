<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index(Contact $contact): JsonResponse
    {
        if ($contact->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'data' => $contact->addresses
        ]);
    }

    public function store(Request $request, Contact $contact): JsonResponse
    {
        if ($contact->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'street' => 'required|string|max:200',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10'
        ]);

        $address = $contact->addresses()->create($validated);

        return response()->json([
            'data' => $address
        ], 201);
    }

    public function show(Contact $contact, Address $address): JsonResponse
    {
        if ($contact->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'data' => $address
        ]);
    }

    public function update(Request $request, Contact $contact, Address $address): JsonResponse
    {
        if ($contact->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'street' => 'string|max:200',
            'city' => 'string|max:100',
            'province' => 'string|max:100',
            'country' => 'string|max:100',
            'postal_code' => 'string|max:10'
        ]);

        $address->update($validated);

        return response()->json([
            'data' => $address
        ]);
    }

    public function destroy(Contact $contact, Address $address): JsonResponse
    {
        if ($contact->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $address->delete();

        return response()->json(null, 204);
    }
}