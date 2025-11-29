<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

use Spatie\Permission\PermissionRegistrar;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

beforeEach(function () {
    $this->seed(\Database\Seeders\PermissionSeeder::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});


// Bind a fake InvoiceSubmissionService for feature tests to avoid external calls
beforeEach(function () {
    $this->instance(\App\Services\InvoiceSubmissionService::class, new class {
        public function submit($invoice, $options = [])
        {
            // Call markAsSubmitted to trigger the observer
            $invoice->markAsSubmitted();

            return [
                'success' => true,
                'submission_id' => 9999,
                'txn_id' => 'TEST-TXN-9999',
            ];
        }
    });
});
// NOTE: InvoiceSubmissionService fakes are applied per-test where needed.

function something()
{
    // ..
}
