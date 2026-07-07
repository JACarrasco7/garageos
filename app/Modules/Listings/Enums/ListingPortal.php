<?php

namespace App\Modules\Listings\Enums;

enum ListingPortal: string
{
    case MOBILE_DE = 'mobile_de';
    case AUTOSCOUT24 = 'autoscout24';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::MOBILE_DE => 'Mobile.de',
            self::AUTOSCOUT24 => 'AutoScout24',
            self::OTHER => 'Otro',
        };
    }

    public function getUrlPattern(): ?string
    {
        return match ($this) {
            self::MOBILE_DE => 'mobile.de',
            self::AUTOSCOUT24 => 'autoscout24.es',
            self::OTHER => null,
        };
    }
}
