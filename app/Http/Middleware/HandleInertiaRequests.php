<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $admin = $request->user('admin');
        $student = $request->user('web');
        $studentUser = $student?->user;

        $guard = null;
        $role = null;
        $authUser = null;

        if ($admin) {
            $guard = 'admin';
            $role = 'admin';
            $authUser = [
                'adminID' => $admin->adminID,
                'studentID' => null,
                'name' => 'Admin',
                'email' => $admin->email,
                'role' => 'admin',
            ];
        } elseif ($student) {
            $guard = 'web';
            $role = $request->session()->get('user_role') ?? $studentUser?->role;
            $authUser = [
                'studentID' => $student->studentID,
                'name' => $student->name,
                'email' => $student->email,
                'role' => $role,
                'model' => $studentUser?->model,
                'plate_number' => $studentUser?->plate_number,
                'is_default' => $studentUser?->is_default,
            ];
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $authUser,
                'role' => fn () => $role,
                'guard' => fn () => $guard,
            ],

            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
