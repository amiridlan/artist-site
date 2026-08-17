<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ] : null,
                'can' => $user ? [
                    'manage-kanban' => $user->can('manage-kanban'),
                    'manage-resources' => $user->can('manage-resources'),
                    'view-all-schedules' => $user->can('view-all-schedules'),
                    'override-conflicts' => $user->can('override-conflicts'),
                    'manage-documents' => $user->can('manage-documents'),
                    'manage-contracts' => $user->can('manage-contracts'),
                    'view-contracts' => $user->can('view-contracts'),
                    'view-reports' => $user->can('view-reports'),
                ] : null,
            ],
            'flash' => [
                'success' => session('success'),
                'error'   => session('error'),
            ],
        ]);
    }
}
