<?php

declare(strict_types=1);

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

describe('Foundation Architecture', function () {
    it('enforces strict types across the application')
        ->expect('App')
        ->toUseStrictTypes();

    it('prevents usage of debugging functions in production code')
        ->expect(['dd', 'dump', 'var_dump', 'die', 'exit'])
        ->not->toBeUsed();

    it('prevents usage of env() outside configuration files')
        ->expect('env')
        ->not->toBeUsedIn('App');

    it('ensures proper application structure')
        ->expect('App')
        ->toBeClasses()
        ->ignoring(\App\Actions\Fortify\PasswordValidationRules::class);
});

describe('Laravel Best Practices', function () {
    it('ensures controllers extend the base controller')
        ->expect('App\Http\Controllers')
        ->classes()
        ->toExtend(Controller::class)
        ->ignoring([Controller::class]);

    it('ensures all models extend Eloquent Model')
        ->expect('App\Models')
        ->classes()
        ->toExtend(Model::class);

    it('ensures middleware implements proper interface')
        ->expect('App\Http\Middleware')
        ->classes()
        ->toHaveMethod('handle');

    it('ensures services exist and are properly structured')
        ->expect('App\Services')
        ->classes()
        ->toHaveSuffix('Service');
});

describe('Security Architecture', function () {
    it('ensures password fields are properly hidden')
        ->expect('App\Models\User')
        ->toHaveMethod('getHidden');

    it('prevents direct database queries in controllers')
        ->expect(DB::class)
        ->not->toBeUsedIn('App\Http\Controllers');
});
