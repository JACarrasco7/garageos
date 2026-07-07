<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import WebLayout from '@/layouts/WebLayout.vue';
import PageCard from '@/Components/PageCard.vue';
import { Button } from '@/Components/ui/button';
import { CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { ArrowLeft, Euro, CheckCircle, XCircle, Clock } from 'lucide-vue-next';

const props = defineProps<{
    transactions: {
        data: Array<{
            id: number;
            type: string;
            amount: number;
            status: string;
            description: string;
            created_at: string;
            listing?: { title: string };
            buyer?: { name: string };
            seller?: { name: string };
        }>;
        current_page: number;
        last_page: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
}>();
</script>

<template>
    <WebLayout>
        <template #header>
            Mis Transacciones
        </template>

        <div class="max-w-4xl mx-auto py-8">
            <PageCard>
                <template #title>
                    <CardHeader>
                        <CardTitle>Mis Transacciones</CardTitle>
                    </CardHeader>
                </template>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Fecha</TableHead>
                                <TableHead>Tipo</TableHead>
                                <TableHead>Descripción</TableHead>
                                <TableHead>Importe</TableHead>
                                <TableHead>Estado</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="tx in transactions.data" :key="tx.id">
                                <TableCell class="text-sm">
                                    {{ new Date(tx.created_at).toLocaleDateString() }}
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline">{{ tx.type === 'offer' ? 'Oferta' : 'Reserva' }}</Badge>
                                </TableCell>
                                <TableCell>
                                    <div class="font-medium">{{ tx.listing?.title || tx.description }}</div>
                                    <div class="text-sm text-muted-foreground">
                                        {{ tx.buyer?.name }} ↔ {{ tx.seller?.name }}
                                    </div>
                                </TableCell>
                                <TableCell class="font-semibold">
                                    {{ tx.amount }} €
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <component
                                            :is="tx.status === 'completed' ? CheckCircle : tx.status === 'rejected' ? XCircle : Clock"
                                            class="w-4 h-4"
                                            :class="tx.status === 'completed' ? 'text-green-500' : tx.status === 'rejected' ? 'text-red-500' : 'text-yellow-500'"
                                        />
                                        <Badge :variant="tx.status === 'completed' ? 'default' : tx.status === 'rejected' ? 'destructive' : 'secondary'">
                                            {{ tx.status }}
                                        </Badge>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <div v-if="transactions.last_page > 1" class="flex justify-center gap-2 mt-6">
                        <Link
                            v-for="link in transactions.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="{ 'opacity-50': !link.url, 'font-bold': link.active }"
                            class="px-3 py-1 text-sm border rounded"
                            v-html="link.label"
                        />
                    </div>
                </CardContent>
            </PageCard>
        </div>
    </WebLayout>
</template>
