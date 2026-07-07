<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Upload } from 'lucide-vue-next';

const page = usePage();
const extractedData = page.props.extracted_data as Record<string, any> | null;

const form = useForm({
    document: null as File | null,
    brand: extractedData?.brand ?? '',
    model: extractedData?.model ?? '',
    year: extractedData?.year ?? null,
    mileage_km: extractedData?.mileage_km ?? null,
    fuel_type: extractedData?.fuel_type ?? '',
    power_hp: extractedData?.power_hp ?? null,
    gearbox: extractedData?.gearbox ?? '',
    co2_emissions: extractedData?.co2_emissions ?? null,
});

const handleUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.document = target.files[0];
        form.post(route('vehicle.documents.ocr.store'));
    }
};
</script>

<template>
    <div class="max-w-2xl mx-auto py-8">
        <Card>
            <CardHeader>
                <CardTitle>Escanear Ficha Técnica</CardTitle>
                <CardDescription>
                    Sube una foto de la ficha técnica del vehículo y los datos se rellenarán automáticamente
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="border-2 border-dashed rounded-lg p-6 text-center">
                    <Input
                        type="file"
                        accept="image/*"
                        @change="handleUpload"
                        class="hidden"
                        id="document"
                    />
                    <Label for="document" class="cursor-pointer">
                        <Upload class="w-8 h-8 mx-auto mb-2" />
                        <span>Subir imagen</span>
                    </Label>
                </div>

                <div v-if="extractedData" class="space-y-4 mt-4">
                    <h3 class="font-semibold">Datos extraídos</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <Label>Marca</Label>
                            <Input v-model="form.brand" readonly />
                        </div>
                        <div>
                            <Label>Modelo</Label>
                            <Input v-model="form.model" readonly />
                        </div>
                        <div>
                            <Label>Año</Label>
                            <Input v-model="form.year" readonly />
                        </div>
                        <div>
                            <Label>Kilometraje</Label>
                            <Input v-model="form.mileage_km" readonly />
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
