<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    // Blade page
    public function page()
    {
        return view('users');
    }

    // JSON for AJAX
    public function index()
    {
        return response()->json($this->userRepo->all());
    }

    // Store user
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $data['password'] = bcrypt($data['password']);
        $user = $this->userRepo->create($data);

        return response()->json(['success' => true, 'user' => $user]);
    }
// Update user
public function update(Request $request, $id)
{
    $data = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email,' . $id,
        'password' => 'nullable|min:6',
    ]);

    if(!empty($data['password'])){
        $data['password'] = bcrypt($data['password']);
    } else {
        unset($data['password']);
    }

    $user = $this->userRepo->find($id);
    $user->update($data);

    return response()->json(['success' => true, 'user' => $user]);
}


    // Delete user
    public function destroy($id)
    {
        $this->userRepo->delete($id);
        return response()->json(['success' => true]);
    }
}
