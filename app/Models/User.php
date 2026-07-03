<?php

namespace App\Models;

use App\Modules\Alerts\Models\AlertRule;
use App\Modules\Alerts\Models\Notification;
use App\Modules\Documents\Models\Document;
use App\Modules\Identity\Models\Garage;
use App\Modules\Maintenance\Models\Workshop;
use App\Modules\Providers\Models\Provider;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

#[Fillable(['name', 'email', 'password', 'fcm_token', 'gdpr_consent', 'gdpr_consent_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use Billable, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gdpr_consent' => 'boolean',
            'gdpr_consent_at' => 'timestamp',
        ];
    }

    public function garages(): HasMany
    {
        return $this->hasMany(Garage::class);
    }

    public function workshop(): HasOne
    {
        return $this->hasOne(Workshop::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function alertRules(): HasMany
    {
        return $this->hasManyThrough(AlertRule::class, Garage::class);
    }

    public function documents(): HasMany
    {
        return $this->hasManyThrough(Document::class, Garage::class);
    }

    public function provider(): HasOne
    {
        return $this->hasOne(Provider::class);
    }
}
