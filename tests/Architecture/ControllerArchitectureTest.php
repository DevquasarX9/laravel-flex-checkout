<?php

declare(strict_types=1);

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;

describe('Controller Architecture', function () {
    it('ensures controllers have proper naming convention')
        ->expect('App\Http\Controllers')
        ->classes()
        ->toHaveSuffix('Controller')
        ->ignoring([Controller::class]);

    it('ensures controllers are only in HTTP layer')
        ->expect('App\Http\Controllers')
        ->toOnlyBeUsedIn([
            'routes',
            'App\Http\Controllers',
            'tests',
        ]);

    it('ensures request classes are properly structured')
        ->expect('App\Http\Requests')
        ->classes()
        ->toExtend(FormRequest::class);

    it('ensures middleware is properly applied')
        ->expect('App\Http\Middleware')
        ->classes()
        ->toHaveMethod('handle');
});

describe('Request and Response Patterns', function () {
    it('ensures form requests have required methods')
        ->expect('App\Http\Requests')
        ->classes()
        ->toHaveMethod('rules');
});
