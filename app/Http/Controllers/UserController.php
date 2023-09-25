<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{

    public function showUsers($accountId)
    {
        $account = Account::findOrFail($accountId);

        $users = $account->users;

        return view('admin.account.showUsers', compact('account', 'users'));
    }

    public function create($accountId)
    {
        // $account_id = $request->accountId;
        $account = Account::find($accountId);
        return view('admin.account.createUser', compact('account'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'account_id' => 'required',
            'name' => 'required|string|max:255',
            'family' => 'required|string|max:255',
            'mobile' => 'required|string|max:11',
            'email' => 'required|string|email|max:255|unique:users',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'postalcode' => 'nullable|string|max:10',
            'username' => 'required|string',
            'password' => 'required|string|min:8|confirmed',

        ]);

        $user = User::create([
            'account_id' => $validatedData['account_id'],
            'name' => $validatedData['name'],
            'family' => $validatedData['family'],
            'mobile' => $validatedData['mobile'],
            'email' => $validatedData['email'],
            'state' => $validatedData['state'],
            'city' => $validatedData['city'],
            'address' => $validatedData['address'],
            'postalcode' => $validatedData['postalcode'],
            'username' => $validatedData['username'],
            'password' => $validatedData['password'],
            'user_status' => 'waiting'
        ]);

        Alert::success('موفق', 'کاربر با موفقیت ایجاد شد.');
        return redirect()->route('user.showUsers', ['accountId' => $validatedData['account_id']]);

    }

    public function editUser($accountId, $userId)
    {
        $account = Account::findOrFail($accountId);
        $user = User::find($userId);

        return view('admin.account.editUser', compact('account', 'user'));
    }

    public function updateUser(Request $request, $accountId, $userId)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'family' => 'required|string|max:255',
            'mobile' => 'required|string|max:11',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'postalcode' => 'nullable|string|max:10',
        ]);

        $user = User::findOrFail($userId);
        $user->update($validatedData);

        Alert::success('موفق', 'حساب کاربری با موفقیت ویرایش شد.');
        return redirect()->back();
    }

    public function destroyUser($accountId, $userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        Alert::success('موفق', 'حساب کاربری با موفقیت حذف شد.');
        return redirect()->back();
    }
}
