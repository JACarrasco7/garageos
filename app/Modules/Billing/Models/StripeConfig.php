<?php

namespace App\Modules\Billing\Models;

use Illuminate\Database\Eloquent\Model;

class StripeConfig extends Model
{
    protected $fillable = [
        'key',
        'secret',
        'webhook_secret',
        'account_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function getKey(): ?string
    {
        return static::first()?->key ?? config('services.stripe.key');
    }

    public static function getSecret(): ?string
    {
        return static::first()?->secret ?? config('services.stripe.secret');
    }

    public static function getWebhookSecret(): ?string
    {
        return static::first()?->webhook_secret ?? config('services.stripe.webhook.secret');
    }

    public static function updateKeys(array $keys): void
    {
        $config = static::firstOrCreate([]);
        $config->update($keys);
    }
}
