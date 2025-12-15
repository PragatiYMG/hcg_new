<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>> [filter_name => classname]
     *                                                     or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'adminauth'     => \App\Filters\AdminAuth::class,
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
        'adminauth' => [
            'before' => [
                'admin/dashboard',
                'admin/profile',
                'admin/profile/*',
                'admin/admin-users',
                'admin/admin-users/*',
                'admin/areas',
                'admin/areas/*',
                'admin/societies',
                'admin/societies/*',
                'admin/customers',
                'admin/customers/*',
                'admin/settings',
                'admin/settings/*',
                'admin/access-management',
                'admin/access-management/*',
                'admin/users',
                'admin/users/*',
                'admin/taxes',
                'admin/taxes/*',
                'admin/rates',
                'admin/rates/*',
                'admin/charges',
                'admin/charges/*',
                'admin/banks',
                'admin/banks/*',
                'admin/images',
                'admin/images/*',
                'admin/bills',
                'admin/bills/*',
                'admin/connection-fees',
                'admin/connection-fees/*',
                'admin/connection-statuses',
                'admin/connection-statuses/*',
                'admin/meter-contractors',
                'admin/meter-contractors/*',
                'admin/meter-manufacturers',
                'admin/meter-manufacturers/*',
                'admin/stove-types',
                'admin/stove-types/*',
                'admin/burner-counts',
                'admin/burner-counts/*',
                'admin/countries',
                'admin/countries/*',
                'admin/states',
                'admin/states/*',
                'admin/cities',
                'admin/cities/*',
                'admin/departments',
                'admin/departments/*',
                'admin/logs'
            ]
        ]
    ];
}
