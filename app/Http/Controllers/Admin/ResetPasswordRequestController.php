<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;

class ResetPasswordRequestController extends Controller
{
   public function index()
{
    $requests = PasswordResetRequest::where('status', 'pending')->get();
    return view('admin.reset_requests.index', compact('requests'));
}

public function approve($id)
{
    $req = PasswordResetRequest::findOrFail($id);
    $req->status = 'approved';
    $req->approved_at = now();
    $req->save();

    return back()->with('success', 'Permintaan disetujui');
}
}

