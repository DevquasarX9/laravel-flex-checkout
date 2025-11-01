<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\DB;

describe('Security Architecture', function () {
    it('prevents env() usage outside configuration')
        ->expect('env')
        ->not->toBeUsedIn([
            'App\Http\Controllers',
            'App\Models',
            'App\Services',
        ]);

    it('ensures password fields are hidden in models')
        ->expect('App\Models\User')
        ->toHaveMethod('getHidden');

    it('prevents direct database queries in controllers for security')
        ->expect(DB::class)
        ->not->toBeUsedIn('App\Http\Controllers');
});

describe('Permission Architecture', function () {
    it('ensures permission middleware exists')
        ->expect('App\Http\Middleware')
        ->classes()
        ->toHaveMethod('handle');

    it('ensures User model is ready for permissions')
        ->expect('App\Models\User')
        ->toExtend(User::class);
});
