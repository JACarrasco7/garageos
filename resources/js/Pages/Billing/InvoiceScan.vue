<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Upload, FileText } from 'lucide-vue-next';

const page = usePage();
const extractedData = page.props.extracted_data as Record<string, any> | null;

const form = useForm({
    invoice: null as File | null,
    invoice_number: extractedData?.invoice_number ?? '',
    invoice_date: extractedData?.invoice_date ?? '',
    due_date: extractedData?.due_date ?? '',
    total_amount: extractedData?.total_amount ?? null,
    tax_amount: extractedData?.tax_amount ?? null,
    issuer_name: extractedData?.issuer_name ?? '',
    issuer_nif: extractedData?.issuer_nif ?? '',
});

const handleUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.invoice = target.files[0];
        form.post(route('billing.invoices.ocr.store'));
    }
};
</script>

<template>
    <div class="max-w-2xl mx-auto py-8">
        <Card>
            <CardHeader>
                <CardTitle>Escanear Factura</CardTitle>
                <CardDescription>
                    Sube una foto de la factura y los datos se rellenarán automáticamente
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="border-2 border-dashed rounded-lg p-6 text-center">
                    <Input
                        type="file"
                        accept="image/*"
                        @change="handleUpload"
                        class="hidden"
                        id="invoice"
                    />
                    <Label for="invoice" class="cursor-pointer">
                        <FileText class="w-8 h-8 mx-auto mb-2" />
                        <span>Subir imagen de factura</span>
                    </Label>
                </div>

                <div v-if="extractedData" class="space-y-4 mt-4">
                    <h3 class="font-semibold">Datos extraídos</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <Label>Número factura</Label>
                            <Input v-model="form.invoice_number" />
                        </div>
                        <div>
                            <Label>Fecha</Label>
                            <Input v-model="form.invoice_date" />
                        </div>
                        <div>
                            <Label>Fecha vencimiento</Label>
                            <Input v-model="form.due_date" />
                        </div>
                        <div>
                            <Label>Importe total</Label>
                            <Input v-model="form.total_amount" type="number" step="0.01" />
                        </div>
                        <div>
                            <Label>IVA</Label>
                            <Input v-model="form.tax_amount" type="number" step="0.01" />
                        </div>
                        <div>
                            <Label>Emisor</Label>
                            <Input v-model="form.issuer_name" />
                        </div>
                        <div class="col-span-2">
                            <Label>NIF emisor</Label>
                            <Input v-model="form.issuer_nif" />
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
