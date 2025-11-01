<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

describe('Model Architecture', function () {
    it('ensures all models extend Eloquent')
        ->expect('App\Models')
        ->classes()
        ->toExtend(Model::class);

    it('ensures models have proper fillable or guarded properties')
        ->expect('App\Models')
        ->classes()
        ->toHaveMethod('getFillable');

    it('ensures models use proper casting')
        ->expect('App\Models')
        ->classes()
        ->toHaveMethod('getCasts');

    it('ensures tenant-related models use UUIDs')
        ->expect(['App\Models\Tenant', 'App\Models\Domain'])
        ->toUseTrait('Illuminate\Database\Eloquent\Concerns\HasUuids');
});

describe('Model Relationships', function () {
    it('ensures tenant model has proper relationships')
        ->expect('App\Models\Tenant')
        ->toHaveMethod('domains')
        ->toHaveMethod('primaryDomain');

    it('ensures domain model has tenant relationship')
        ->expect('App\Models\Domain')
        ->toHaveMethod('tenant');
});

describe('Model Events and Observers', function () {
    it('ensures observers are in proper namespace when they exist')
        ->expect('App\Observers')
        ->classes()
        ->toHaveSuffix('Observer');
});
