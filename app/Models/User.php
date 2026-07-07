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
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'fcm_token', 'gdpr_consent', 'gdpr_consent_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use Billable, HasFactory, HasRoles, Notifiable;

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

    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class);
    }

    // Helper methods for role checking
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isImporter(): bool
    {
        return $this->hasRole('importer');
    }

    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    // Check if user has a specific feature
    public function hasFeature(string $feature): bool
    {
        $plan = $this->subscription('default')?->stripe_price ?? 'free';
        $features = config("subscription.features.{$feature}.plans", []);

        return in_array($plan, $features);
    }

    // Get vehicle limit from current plan
    public function getVehicleLimit(): int|null
    {
        $plan = $this->subscription('default')?->stripe_price ?? 'free';
        return config("subscription.features.vehicles.create.limits.{$plan}");
    }

    // Check if user can create more vehicles
    public function canCreateVehicle(): bool
    {
        $limit = $this->getVehicleLimit();
        if ($limit === null) return true;

        return $this->garages()->withCount('vehicles')->get()->sum('vehicles_count') < $limit;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'admin'));
    }

    public function scopeImporters($query)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'importer'));
    }
}
