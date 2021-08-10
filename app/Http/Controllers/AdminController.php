<?php

namespace App\Http\Controllers;

use App\Mail\Invite;
use App\Models\User;
use App\Notifications\InviteUser;
use App\Notifications\VarifyUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['user_name' => $request->user_name, 'password' => $request->password])) {
            return Auth::user();
        }
    }

    public function logout()
    {
        Auth::logout();
        return "logout success";
    }

    public function user_register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'user_name' => 'required|min:4|max:20',
            'email' => 'email|unique:users',
            'avatar' => 'required',
            'password' => 'required',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->user_name = $request->user_name;
        $user->email = $request->email;
        $user->user_role = 'user';
        $user->avatar = 'user.png';
        $user->registered_at = Carbon::now();
        $user->pin = 123456;
        $user->password = Hash::make($request->password);
        if ($user->save()) {
            $latest_user = $user->latest()->first();
            $latest_user->notify(new VarifyUser($latest_user->pin));
        }
        return "success";
    }

    public function invite_user(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $address = $request->email;
        Mail::send(new Invite($request->email));
        return $request->email;
    }

    public function user_varify()
    {
        $varification_code = \Illuminate\Support\Facades\Request::get('code');
        $user = User::where(['pin' => $varification_code])->first();
        if ($user != null) {
            $user->isVarified = true;
            $user->save();
            return "success";
        } else {
            return "Failed";
        }
    }

    public function user_update(Request $request)
    {
        // return $request->all();
        $user = User::where('email', $request->old_email)->first();
        if ($request->name) {
            $user->name = $request->name;
        }
        if ($request->email) {
            $user->email = $request->email;
        }
        if ($request->user_name) {
            $user->user_name = $request->user_name;
        }
        $user->save();
        return "update success";
    }
}
