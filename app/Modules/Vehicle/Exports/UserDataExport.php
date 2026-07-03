<?php

namespace App\Modules\Vehicle\Exports;

use App\Models\User;
use App\Modules\Documents\Models\Document;
use App\Modules\Maintenance\Models\MaintenanceEntry;
use App\Modules\Vehicle\Models\Vehicle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UserDataExport implements WithMultipleSheets
{
    public function __construct(public int $userId) {}

    public function sheets(): array
    {
        return [
            new UserSheet($this->userId),
            new VehiclesSheet($this->userId),
            new DocumentsSheet($this->userId),
            new MaintenanceSheet($this->userId),
        ];
    }
}

class UserSheet extends UserDataExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::where('id', $this->userId)->get(['name', 'email', 'created_at']);
    }

    public function headings(): array
    {
        return ['Nombre', 'Email', 'Fecha registro'];
    }
}

class VehiclesSheet extends UserDataExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Vehicle::whereHas('garage', fn ($q) => $q->where('user_id', $this->userId))
            ->get(['plate', 'brand', 'model', 'year', 'fuel_type', 'current_km']);
    }

    public function headings(): array
    {
        return ['Matrícula', 'Marca', 'Modelo', 'Año', 'Combustible', 'Km'];
    }
}

class DocumentsSheet extends UserDataExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Document::whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $this->userId))
            ->get(['type', 'title', 'document_date', 'expiry_date', 'amount']);
    }

    public function headings(): array
    {
        return ['Tipo', 'Título', 'Fecha', 'Vencimiento', 'Importe'];
    }
}

class MaintenanceSheet extends UserDataExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return MaintenanceEntry::whereHas('vehicle.garage', fn ($q) => $q->where('user_id', $this->userId))
            ->get(['type', 'title', 'service_date', 'km_at_service', 'cost']);
    }

    public function headings(): array
    {
        return ['Tipo', 'Título', 'Fecha', 'Km', 'Coste'];
    }
}
