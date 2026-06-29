<?php

namespace App\Enums;

enum AlertType: string
{
    case ITV = 'itv';
    case Insurance = 'seguro';
    case Oil = 'aceite';
    case Tires = 'neumaticos';
    case Revision = 'revision';
    case Tax = 'impuesto';
    case Battery = 'bateria';
    case Custom = 'custom';
}
