<?php

namespace App\Modules\VehicleImport\Enums;

enum ImportStep: string
{
    case PURCHASE = 'purchase';
    case TRANSPORT = 'transport';
    case ITV_INSPECTION = 'itv_inspection';
    case TAXES = 'taxes';
    case DGT_REGISTRATION = 'dgt_registration';
    case PLATES = 'plates';
    case COMPLETED = 'completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::PURCHASE => 'Compra en Alemania',
            self::TRANSPORT => 'Transporte a España',
            self::ITV_INSPECTION => 'ITV de Importación',
            self::TAXES => 'Liquidación de Impuestos',
            self::DGT_REGISTRATION => 'Matriculación DGT',
            self::PLATES => 'Placas Físicas',
            self::COMPLETED => 'Completado',
        };
    }

    public function getOrder(): int
    {
        return match ($this) {
            self::PURCHASE => 1,
            self::TRANSPORT => 2,
            self::ITV_INSPECTION => 3,
            self::TAXES => 4,
            self::DGT_REGISTRATION => 5,
            self::PLATES => 6,
            self::COMPLETED => 7,
        };
    }

    public function getNext(): ?self
    {
        return match ($this) {
            self::PURCHASE => self::TRANSPORT,
            self::TRANSPORT => self::ITV_INSPECTION,
            self::ITV_INSPECTION => self::TAXES,
            self::TAXES => self::DGT_REGISTRATION,
            self::DGT_REGISTRATION => self::PLATES,
            self::PLATES => self::COMPLETED,
            self::COMPLETED => null,
        };
    }

    public function getPrevious(): ?self
    {
        return match ($this) {
            self::PURCHASE => null,
            self::TRANSPORT => self::PURCHASE,
            self::ITV_INSPECTION => self::TRANSPORT,
            self::TAXES => self::ITV_INSPECTION,
            self::DGT_REGISTRATION => self::TAXES,
            self::PLATES => self::DGT_REGISTRATION,
            self::COMPLETED => self::PLATES,
        };
    }

    public function getRequiredDocuments(): array
    {
        return match ($this) {
            self::PURCHASE => [
                'compraventa',
                'coc',
                'ficha_tecnica_origen',
                'tarjeta_itv_origen',
            ],
            self::TRANSPORT => [
                'seguro_transporte',
            ],
            self::ITV_INSPECTION => [
                'ficha_itv_es',
            ],
            self::TAXES => [
                'modelo_576',
                'modelo_309_300',
                'modelo_itp',
                'justificante_ivtm',
            ],
            self::DGT_REGISTRATION => [
                'permiso_circulacion',
            ],
            self::PLATES => [],
            self::COMPLETED => [],
        };
    }
}
