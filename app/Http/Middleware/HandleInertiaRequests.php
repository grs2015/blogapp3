<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => function () use ($request) {
                return [
                    'user' => $request->user() ? [
                        'id' => $request->user()->id,
                        'first_name' => $request->user()->first_name,
                        'last_name' => $request->user()->last_name,
                        'full_name' => $request->user()->full_name,
                        'email' => $request->user()->email,
                        'status' => $request->user()->status,
                        'role' => $request->user()->roles->value('name'),
                        // 'role' => $request->user()->getRoleNames(),
                        'email_verified_at' => $request->user()->email_verified_at,
                        'avatar' => $request->user()->avatar
                    ] : null,
                ];
            },
            'flash' => function () use ($request) {
                return [
                    'register' => $request->session()->get('register'),
                    'status' => $request->session()->get('status')
                ];
            },
            'can' => $request->user() ? [
                'create_post' => Auth::user()->can('create post'),
                'change_status_to_pending' => Auth::user()->can('change post status to pending'),
                'change_status_to_published' => Auth::user()->can('change post status to published'),
                'change_status_to_unpublished' => Auth::user()->can('change post status to unpublished'),
                'create_user' => Auth::user()->can('create user'),
                'update_settings' => Auth::user()->can('update baseinfo'),
                'view_comments' => Auth::user()->can('view post comments'),
                'change_comments_status' => Auth::user()->can('change comments status'),
                'see_credentials' => Auth::user()->can('have credentials shown')
            ] : null,
            'search' => $request->query('search'),
            // 'sorting' => function() use ($request) {
            //     return [
            //         'column' => $request->query('column'),
            //         'descending' => $request->query('descending')
            //     ];
            // }
        ]);
    }
}
