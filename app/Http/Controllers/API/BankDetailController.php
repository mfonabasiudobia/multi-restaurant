<?php
namespace App\Http\Controllers\API;

use App\Models\BankDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankDetailController extends Controller
{
    /**
     * Get bank details publicly
     */
    public function getBankDetails()
    {
        // Retrieve the first bank details entry (assuming you store one set of bank details)
        $bankDetails = BankDetail::first();

        if ($bankDetails) {
            return response()->json([
                'status' => 'success',
                'data' => $bankDetails
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Bank details not found'
        ], 404);
    }
}