<?php

namespace App\Http\Controllers;

use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class InviteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $invites = Invite::all();
        return response()->json([
            'message' => 'تم جلب قائمة المدعوين بنجاح',
            'Invites' => $invites
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        return response()->json([
            'message' => 'لا يدعم هذا المنصة'
        ], 405);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'description' => 'required|string',
            'phone' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'فشل في التحقق من صحة البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        $invite = Invite::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'description' => $request->description,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'message' => 'تم إضافة المدعو بنجاح',
            'Invite' => $invite
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $invite = Invite::find($id);

        if (!$invite) {
            return response()->json([
                'message' => 'المدعو غير موجود'
            ], 404);
        }

        return response()->json([
            'message' => 'تم العثور على بيانات المدعو بنجاح',
            'Invite' => $invite
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        return response()->json([
            'message' => 'لا يدعم هذا المنصة'
        ], 405);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $invite = Invite::find($id);

        if (!$invite) {
            return response()->json([
                'message' => 'المدعو غير موجود'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'phone' => 'sometimes|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'فشل في التحقق من صحة البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the validated data
        $updateData = $request->only([
            'first_name',
            'last_name',
            'description',
            'phone'
        ]);

        // If no fields to update
        if (empty($updateData)) {
            return response()->json([
                'message' => 'لم يتم تقديم أي بيانات للتحديث',
                'received_data' => $request->all() // Debug: show what data was received
            ], 422);
        }

        // Perform the update
        $invite->update($updateData);
        
        // Refresh the model to get the latest data
        $invite->refresh();

        return response()->json([
            'message' => 'تم تحديث بيانات المدعو بنجاح',
            'Invite' => $invite
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $invite = Invite::find($id);

        if (!$invite) {
            return response()->json([
                'message' => 'المدعو غير موجود'
            ], 404);
        }

        $invite->delete();

        return response()->json([
            'message' => 'تم حذف المدعو بنجاح'
        ], 200);
    }
}
