<?php
namespace App\Repositories;

use App\Models\User;

class UserRepository implements UserRepositoryInterface {

    public function all() {
        return User::latest()->get();
    }

    public function find($id) {
        return User::find($id);
    }

    public function create(array $data) {
        return User::create($data);
    }

    public function delete($id) {
        return User::destroy($id);
    }
}
