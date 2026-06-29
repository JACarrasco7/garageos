<?php

namespace App\Enums;

enum DocumentType: string
{
    case Invoice = 'factura';
    case ITV = 'itv';
    case Insurance = 'seguro';
    case Tax = 'impuesto';
    case Other = 'otro';
}