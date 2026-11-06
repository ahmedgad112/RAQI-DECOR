<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminPermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private UserService $users) {}

    public function index(): View
    {
        $users = User::query()
            ->with('profile')
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'permissionOptions' => AdminPermission::options(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->users->create(
            [
                ...$request->safe()->except(['avatar', 'password_confirmation']),
                'is_admin' => $request->boolean('is_admin'),
                'permissions' => $request->input('permissions', []),
            ],
            $request->file('avatar'),
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تم إنشاء المستخدم والبروفايل بنجاح.');
    }

    public function edit(User $user): View
    {
        $user->loadMissing('profile');

        return view('admin.users.edit', [
            'user' => $user,
            'permissionOptions' => AdminPermission::options(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->users->update(
            $user,
            [
                ...$request->safe()->except(['avatar', 'password_confirmation']),
                'is_admin' => $request->boolean('is_admin'),
                'permissions' => $request->input('permissions', []),
                'remove_avatar' => $request->boolean('remove_avatar'),
            ],
            $request->file('avatar'),
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تم تحديث المستخدم والصلاحيات بنجاح.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'لا يمكن حذف حسابك الحالي.');
        }

        $this->users->delete($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح.');
    }
}
