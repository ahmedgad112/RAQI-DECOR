<?php

namespace App\Services;

use App\Enums\AdminPermission;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function __construct(private ImageService $images) {}

    /**
     * @param  array{
     *     name: string,
     *     email: string,
     *     password: string,
     *     is_admin?: bool,
     *     permissions?: list<string>|null,
     *     phone?: string|null,
     *     job_title?: string|null,
     *     bio?: string|null
     * }  $data
     */
    public function create(array $data, ?UploadedFile $avatar = null): User
    {
        return DB::transaction(function () use ($data, $avatar) {
            $isAdmin = (bool) ($data['is_admin'] ?? false);

            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'is_admin' => $isAdmin,
                'permissions' => $this->normalizePermissions($isAdmin, $data['permissions'] ?? []),
                'email_verified_at' => now(),
            ]);

            $profileData = [
                'phone' => $data['phone'] ?? null,
                'job_title' => $data['job_title'] ?? null,
                'bio' => $data['bio'] ?? null,
            ];

            if ($avatar !== null) {
                $stored = $this->images->store($avatar, 'avatars');
                $profileData['avatar_path'] = $stored['path_medium'];
            }

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData,
            );

            return $user->load('profile');
        });
    }

    /**
     * @param  array{
     *     name: string,
     *     email: string,
     *     password?: string|null,
     *     is_admin?: bool,
     *     permissions?: list<string>|null,
     *     phone?: string|null,
     *     job_title?: string|null,
     *     bio?: string|null,
     *     remove_avatar?: bool
     * }  $data
     */
    public function update(User $user, array $data, ?UploadedFile $avatar = null): User
    {
        return DB::transaction(function () use ($user, $data, $avatar) {
            $isAdmin = (bool) ($data['is_admin'] ?? false);

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'is_admin' => $isAdmin,
                'permissions' => $this->normalizePermissions($isAdmin, $data['permissions'] ?? []),
            ];

            if (! blank($data['password'] ?? null)) {
                $userData['password'] = $data['password'];
            }

            $user->update($userData);

            /** @var Profile $profile */
            $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

            $profileData = [
                'phone' => $data['phone'] ?? null,
                'job_title' => $data['job_title'] ?? null,
                'bio' => $data['bio'] ?? null,
            ];

            $previousAvatar = $profile->avatar_path;

            if ($avatar !== null) {
                $stored = $this->images->store($avatar, 'avatars');
                $profileData['avatar_path'] = $stored['path_medium'];
            } elseif (! empty($data['remove_avatar']) && $previousAvatar) {
                $profileData['avatar_path'] = null;
            }

            $profile->update($profileData);

            if (
                isset($profileData['avatar_path'])
                && $previousAvatar
                && $previousAvatar !== ($profileData['avatar_path'] ?? null)
            ) {
                Storage::disk('public')->delete($previousAvatar);
            }

            return $user->load('profile');
        });
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $avatarPath = $user->profile?->avatar_path;

            $user->delete();

            if ($avatarPath) {
                Storage::disk('public')->delete($avatarPath);
            }
        });
    }

    /**
     * @param  list<string>|mixed  $permissions
     * @return list<string>
     */
    private function normalizePermissions(bool $isAdmin, mixed $permissions): array
    {
        if (! $isAdmin) {
            return [];
        }

        if (! is_array($permissions)) {
            return [];
        }

        return array_values(array_intersect($permissions, AdminPermission::values()));
    }
}
