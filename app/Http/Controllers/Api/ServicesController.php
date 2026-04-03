<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServicesController extends Controller
{
    // GET /api/v1/services (public)
    public function index()
    {
        $services = Service::all();
        return response()->json(['services' => $services]);
    }
}
