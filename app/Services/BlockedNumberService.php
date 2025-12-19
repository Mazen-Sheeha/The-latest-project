<?php

namespace App\Services;

use App\Http\Requests\BlockedNumber\CreateBlockedNumberRequest;
use App\Http\Requests\BlockedNumber\DeleteBlockedNumberRequest;
use App\Models\BlockedNumber;

class BlockedNumberService
{
    public function store(CreateBlockedNumberRequest $request)
    {
        $validated = $request->validated();
        BlockedNumber::create($validated);
        return response()->json(['success' => true, 'message' => 'تم حظر هذا الرقم بنجاح']);
    }

    public function destroy(DeleteBlockedNumberRequest $request)
    {
        $validated = $request->validated();
        BlockedNumber::where("phone", $validated['phone'])->delete();
        return response()->json(['success' => true, 'message' => 'تم رفع الحظر عن الرقم بنجاح']);
    }
}
