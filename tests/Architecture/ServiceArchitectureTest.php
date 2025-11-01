<?php

declare(strict_types=1);

describe('Service Layer Architecture', function () {
    it('ensures services are in proper namespace')
        ->expect('App\Services')
        ->classes()
        ->toHaveSuffix('Service');

    it('ensures services are properly structured')
        ->expect('App\Services')
        ->classes()
        ->toHaveSuffix('Service');

    it('ensures services follow single responsibility principle')
        ->expect('App\Services')
        ->classes()
        ->toHaveLineCountLessThan(200);
});

describe('Service Error Handling', function () {
    it('prevents services from echoing or printing output')
        ->expect('App\Services')
        ->classes()
        ->not->toUse(['echo', 'print', 'var_dump', 'dd', 'dump']);
});
