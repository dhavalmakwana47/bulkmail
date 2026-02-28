<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Resolution;
use App\Models\User;
use App\Models\UserCompanyMap;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [];
        $user = auth()->user();
  
        return view('app.index', $data);
    }
}
