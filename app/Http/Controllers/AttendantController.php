<?php

namespace App\Http\Controllers;

use App\Models\Attendant;
use Illuminate\Http\JsonResponse;

class AttendantController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Attendant::all());
    }
}