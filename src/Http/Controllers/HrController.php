<?php
namespace ME\Hr\Http\Controllers;

use Illuminate\Routing\Controller;

class HrController extends Controller
{
    public function index()
    {
        return redirect()->route('hr-center.dashboard');
    }
}
