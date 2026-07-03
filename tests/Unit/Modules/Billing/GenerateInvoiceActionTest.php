<?php

use App\Models\User;
use App\Modules\Billing\Actions\GenerateInvoiceAction;
use App\Modules\Billing\Models\PlatformFee;
use App\Modules\Billing\Models\StripeAccount;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::disk('public')->put('test.pdf', 'test content');
});

test('generates invoice pdf for platform fee', function () {
    $user = User::factory()->create();
    $account = StripeAccount::factory()->create([
        'user_id' => $user->id,
        'stripe_account_id' => 'acct_test123',
    ]);

    $fee = PlatformFee::factory()->create([
        'stripe_account_id' => $account->stripe_account_id,
        'amount' => 100.00,
        'currency' => 'EUR',
        'description' => 'Test fee',
    ]);

    $action = new GenerateInvoiceAction;
    $path = $action->execute($fee);

    expect($path)->toContain('invoices/invoice-');
    expect(Storage::disk('public')->exists($path))->toBeTrue();
    expect($fee->fresh()->invoice_path)->toBe($path);
});

test('invoice path is stored on fee model', function () {
    $user = User::factory()->create();
    $account = StripeAccount::factory()->create([
        'user_id' => $user->id,
        'stripe_account_id' => 'acct_test456',
    ]);

    $fee = PlatformFee::factory()->create([
        'stripe_account_id' => $account->stripe_account_id,
        'amount' => 250.50,
    ]);

    $action = new GenerateInvoiceAction;
    $action->execute($fee);

    $freshFee = $fee->fresh();
    expect($freshFee->invoice_path)->not->toBeNull();
    expect($freshFee->invoice_path)->toContain('.pdf');
});
