<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import WebLayout from '@/layouts/WebLayout.vue';
import Card from '@/Components/ui/card/Card.vue';
import CardHeader from '@/Components/ui/card/CardHeader.vue';
import CardTitle from '@/Components/ui/card/CardTitle.vue';
import CardContent from '@/Components/ui/card/CardContent.vue';
import { Bell, CheckCircle, AlertTriangle } from 'lucide-vue-next';
import Badge from '@/Components/ui/badge/Badge.vue';
import { cn } from '@/lib/utils';

defineProps<{
    alerts?: {
        data: Array<{
            id: number;
            title: string;
            body: string;
            read_at: string | null;
            created_at: string;
            type: string;
        }>;
        links: any[];
    };
}>();
</script>

<template>
  <Head title="Alertas" />

  <WebLayout>
    <template #header>
      Alertas
    </template>

    <Card class="border backdrop-blur-xl bg-card/80">
      <CardHeader>
        <CardTitle class="text-lg font-semibold tracking-tight text-foreground">
          Alertas y Notificaciones
        </CardTitle>
      </CardHeader>
      <CardContent>
        <div v-if="!alerts?.data?.length" class="py-12 text-center">
          <Bell class="mx-auto h-12 w-12 text-muted-foreground/30" />
          <p class="mt-3 text-sm text-muted-foreground">
            No tienes alertas pendientes
          </p>
          <p class="mt-1 text-xs text-muted-foreground/60">
            Las alertas de mantenimiento y caducidad aparecerán aquí
          </p>
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="alert in alerts.data"
            :key="alert.id"
            :class="cn(
              'group flex items-start gap-3 rounded-xl border p-4 transition-all duration-300',
              alert.read_at
                ? 'border-border/50 bg-muted/30 opacity-60'
                : 'border-primary/30 bg-primary/5'
            )"
          >
            <div
              :class="cn(
                'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl transition-transform duration-300 group-hover:scale-105',
                alert.read_at ? 'bg-muted/50 text-muted-foreground' : 'bg-primary/10 text-primary'
              )"
            >
              <AlertTriangle v-if="!alert.read_at" class="h-5 w-5" />
              <CheckCircle v-else class="h-5 w-5" />
            </div>
            <div class="flex-1 space-y-1">
              <div class="flex items-start justify-between gap-2">
                <p class="text-sm font-semibold transition-colors" :class="alert.read_at ? 'text-muted-foreground' : 'text-foreground'">
                  {{ alert.title }}
                </p>
                <Badge v-if="!alert.read_at" variant="default" class="shrink-0">
                  Nueva
                </Badge>
              </div>
              <p class="text-xs text-muted-foreground">
                {{ alert.body }}
              </p>
              <p class="text-[10px] uppercase tracking-wider text-muted-foreground/60">
                {{ new Date(alert.created_at).toLocaleDateString('es-ES', {
                  day: '2-digit',
                  month: 'short',
                  year: 'numeric',
                }) }}
              </p>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  </WebLayout>
</template>
