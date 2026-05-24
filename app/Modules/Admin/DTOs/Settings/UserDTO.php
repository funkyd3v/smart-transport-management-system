<?php

declare(strict_types=1);

namespace App\Modules\Admin\DTOs\Settings;

use App\Modules\Admin\Http\Requests\Settings\StoreUserRequest;
use App\Modules\Admin\Http\Requests\Settings\UpdateUserRequest;

final readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $role,
        public ?string $password,
        public bool $isActive,
    ) {}

    public static function fromStoreRequest(StoreUserRequest $request): self
    {
        return new self(
            name: (string) $request->validated('name'),
            email: (string) $request->validated('email'),
            role: (string) $request->validated('role'),
            password: (string) $request->validated('password'),
            isActive: (bool) ($request->validated('is_active') ?? false),
        );
    }

    public static function fromUpdateRequest(UpdateUserRequest $request): self
    {
        $password = $request->validated('password');

        return new self(
            name: (string) $request->validated('name'),
            email: (string) $request->validated('email'),
            role: (string) $request->validated('role'),
            password: is_string($password) && $password !== '' ? $password : null,
            isActive: (bool) ($request->validated('is_active') ?? false),
        );
    }
}
