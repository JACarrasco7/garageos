<script setup lang="ts">
import { ref } from 'vue';
import WebLayout from '@/layouts/WebLayout.vue';
import PageCard from '@/Components/PageCard.vue';
import { CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { HelpCircle, Car, FileText, Shield, Truck, Euro } from 'lucide-vue-next';

const faqs = [
    {
        category: 'Compras',
        icon: Car,
        items: [
            {
                question: '¿Cómo funciona el proceso de importación?',
                answer: 'El proceso consta de 6 pasos: compra en Alemania, transporte, ITV, impuestos, DGT y matrícula. Cada paso se completa con documentos que subes al sistema.',
            },
            {
                question: '¿Qué documentos necesito para importar?',
                answer: 'Factura original, certificado de importación, ITV, documentación de seguros y papeles del vendedor.',
            },
        ],
    },
    {
        category: 'Pagos',
        icon: Euro,
        items: [
            {
                question: '¿Cuándo se paga cada hito?',
                answer: '20% al firmar el contrato, 50% al certificar la compra, 30% al entregar el vehículo.',
            },
            {
                question: '¿Cómo funciona la protección de pagos?',
                answer: 'Los pagos se retienen hasta que se completa cada hito. El último pago se libera solo cuando confirms recepción.',
            },
        ],
    },
    {
        category: 'Documentos',
        icon: FileText,
        items: [
            {
                question: '¿Cómo subo los documentos?',
                answer: 'Puedes subir fotos de la factura, ITV y otros documentos directamente desde el wizard.',
            },
            {
                question: '¿Los documentos se almacenan de forma segura?',
                answer: 'Sí, todos los documentos se cifran y almacenan en servidores seguros con acceso restringido.',
            },
        ],
    },
    {
        category: 'Seguridad',
        icon: Shield,
        items: [
            {
                question: '¿Es seguro el sistema?',
                answer: 'Sí, utilizamos encriptación de nivel bancario y verificación de identidad.',
            },
            {
                question: '¿Qué pasa si hay un problema?',
                answer: 'Tienes acceso a soporte 24/7 y un sistema de disputas para resolver incidencias.',
            },
        ],
    },
];

const openItem = ref<string | null>(null);
</script>

<template>
    <WebLayout>
        <template #header>
            Preguntas Frecuentes
        </template>

        <div class="max-w-4xl mx-auto py-8">
            <div class="flex items-center gap-3 mb-6">
                <HelpCircle class="w-8 h-8 text-primary" />
                <h1 class="text-2xl font-bold">Preguntas Frecuentes</h1>
            </div>

            <div class="space-y-6">
                <PageCard v-for="(category, cIdx) in faqs" :key="cIdx">
                    <template #title>
                        <CardHeader class="pb-3">
                            <CardTitle class="flex items-center gap-3 text-lg">
                                <component :is="category.icon" class="w-5 h-5 text-primary" />
                                {{ category.category }}
                            </CardTitle>
                        </CardHeader>
                    </template>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="(item, iIdx) in category.items" :key="iIdx" class="border-b last:border-0 pb-4 last:pb-0">
                                <h3 class="font-medium mb-2">{{ item.question }}</h3>
                                <p class="text-sm text-muted-foreground">{{ item.answer }}</p>
                            </div>
                        </div>
                    </CardContent>
                </PageCard>
            </div>

            <PageCard class="mt-8">
                <template #title>
                    <CardHeader>
                        <CardTitle>Contacto</CardTitle>
                    </CardHeader>
                </template>
                <CardContent>
                    <p class="text-muted-foreground mb-4">
                        ¿No encuentras lo que buscas? Contáctanos directamente.
                    </p>
                    <div class="space-y-2 text-sm">
                        <p><strong>Email:</strong> soporte@garageos.com</p>
                        <p><strong>Teléfono:</strong> 600 000 000</p>
                        <p><strong>Horario:</strong> Lunes-Viernes 9:00-18:00</p>
                    </div>
                </CardContent>
            </PageCard>
        </div>
    </WebLayout>
</template>
