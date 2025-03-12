<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\ContactResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
* @OA\Get(
*       path="/",
*       description="Home page",
*       @OA\Response(response="default", description="Welcome page")
* )
*/
/**
* @OA\Tag(name="Contacts")
*/
/**
* @OA\Schema(
*     schema="Contact",
*     type="object",
*     required={"first_name"},
*     @OA\Property(property="id", type="integer", example=1),
*     @OA\Property(property="first_name", type="string", maxLength=50, example="John"),
*     @OA\Property(property="last_name", type="string", maxLength=50, example="Doe"),
*     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
*     @OA\Property(property="phone", type="string", maxLength=20, example="08123456789")
* )
*
* @OA\Schema(
*     schema="ContactCreate",
*     allOf={@OA\Schema(ref="#/components/schemas/Contact")},
*     @OA\Property(property="user_id", type="integer", example=1)
* )
*/

class ContactController extends Controller
{
        /**
     * @OA\Get(
     *     path="/contacts",
     *     summary="Mengambil daftar semua kontak",
     *     tags={"Contacts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar kontak berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Contact")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function index(): JsonResponse
    {
        $contacts = Auth::user()->contacts()->with('addresses')->get();
        return response()->json([
            'data' => $contacts
        ]);
    }

        /**
     * @OA\Post(
     *     path="/contacts",
     *     summary="Membuat kontak baru",
     *     tags={"Contacts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","phone"},
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="081234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Kontak berhasil dibuat",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Contact")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validasi gagal"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:20'
        ]);

        $contact = Auth::user()->contacts()->create($validated);

        return response()->json([
            'data' => $contact
        ], 201);
    }

    public function show(Contact $contact): JsonResponse
    {
        if ($contact->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json([
            'data' => $contact->load('addresses')
        ]);
    }

    public function update(Request $request, Contact $contact): JsonResponse
    {
        if ($contact->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'first_name' => 'string|max:100',
            'last_name' => 'string|max:100',
            'email' => 'email',
            'phone' => 'string|max:20'
        ]);

        $contact->update($validated);

        return response()->json([
            'data' => $contact
        ]);
    }

    public function destroy(Contact $contact): JsonResponse
    {
        if ($contact->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $contact->delete();

        return response()->json(null, 204);
    }
}