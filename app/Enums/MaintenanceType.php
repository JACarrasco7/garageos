<?php

namespace App\Enums;

enum MaintenanceType: string
{
    case Oil = 'aceite';
    case Filters = 'filtros';
    case Tires = 'neumaticos';
    case Brakes = 'frenos';
    case Timing = 'distribucion';
    case Clutch = 'embrague';
    case Battery = 'bateria';
    case ITV = 'itv';
    case GeneralRevision = 'revision_general';
    case Other = 'otro';
}