<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\UserService;

class UserController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        return $this->userService = $userService;
    }

    public function index()
    {
        return $this->userService->index();
    }

    public function create()
    {
        return $this->userService->create();
    }

    public function store(CreateUserRequest $request)
    {
        return $this->userService->store($request);
    }

    public function edit(string $id)
    {
        return $this->userService->edit($id);
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        return $this->userService->update($request, $id);
    }

    public function destroy(string $id)
    {
        return $this->userService->destroy($id);
    }
}
