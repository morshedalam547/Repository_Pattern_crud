<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function all();
    public function create(array $data);
    public function delete($id);
}
