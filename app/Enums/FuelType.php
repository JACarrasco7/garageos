<?php

namespace App\Enums;

enum FuelType: string
{
    case Gasoline = 'gasolina';
    case Diesel = 'diesel';
    case Hybrid = 'hibrido';
    case Electric = 'electrico';
    case GLP = 'glp';
}
