<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function resetPassword(User $user)
    {
        // blok admin reset admin (opsional)
        if ($user->role_name === 'admin') {
            return back()->with('error', 'Tidak bisa reset password admin.');
        }

        $defaultPassword = 'ptm12345';

        $user->password = Hash::make($defaultPassword);
        $user->force_change_password = true;
        $user->save();

        // tandai request sebagai selesai
        PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update(['status' => 'done']);

        return back()->with(
            'success',
            'Password berhasil direset ke: '.$defaultPassword
        );
    }
}
