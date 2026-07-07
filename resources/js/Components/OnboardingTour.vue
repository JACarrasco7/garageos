<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Car, FileText, Wrench, Bell, ShoppingBag, Truck, User, HelpCircle } from 'lucide-vue-next';

interface TourStep {
  target: string;
  title: string;
  content: string;
  icon?: any;
}

const steps: TourStep[] = [
  {
    target: '[data-tour="vehicles"]',
    title: 'Tus vehículos',
    content: 'Añade tu primer vehículo para empezar a registrar su historial.',
    icon: Car,
  },
  {
    target: '[data-tour="documents"]',
    title: 'Documentos',
    content: 'Guarda facturas, ITV y seguros. Te avisaremos cuando caduquen.',
    icon: FileText,
  },
  {
    target: '[data-tour="maintenance"]',
    title: 'Mantenimiento',
    content: 'Registra revisiones y averías para llevar el control del coche.',
    icon: Wrench,
  },
  {
    target: '[data-tour="alerts"]',
    title: 'Alertas',
    content: 'Recibe notificaciones de vencimientos y mantenimientos.',
    icon: Bell,
  },
  {
    target: '[data-tour="marketplace"]',
    title: 'Mercado de venta',
    content: 'Publica tu vehículo para venta al mejor precio. Encuentra ofertas de compra.',
    icon: ShoppingBag,
  },
  {
    target: '[data-tour="imports"]',
    title: 'Importaciones',
    content: 'Gestiona todo el proceso de importación de tu vehículo desde Alemania.',
    icon: Truck,
  },
  {
    target: '[data-tour="help"]',
    title: 'Ayuda y soporte',
    content: 'Accede a preguntas frecuentes y contacta con soporte cuando lo necesites.',
    icon: HelpCircle,
  },
];

const isVisible = ref(false);
const currentStep = ref(0);
const highlightStyle = ref<Record<string, string>>({});
const tooltipStyle = ref<Record<string, string>>({});

const startTour = () => {
  isVisible.value = true;
  currentStep.value = 0;
  positionHighlight();
};

const nextStep = () => {
  if (currentStep.value < steps.length - 1) {
    currentStep.value++;
    positionHighlight();
  } else {
    finishTour();
  }
};

const prevStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
    positionHighlight();
  }
};

const finishTour = () => {
  localStorage.setItem('onboarding-completed', 'true');
  isVisible.value = false;
};

const positionHighlight = () => {
  const target = steps[currentStep.value].target;
  const element = document.querySelector(target) as HTMLElement;

  if (element) {
    const rect = element.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    highlightStyle.value = {
      top: `${rect.top + scrollTop - 10}px`,
      left: `${rect.left - 10}px`,
      width: `${rect.width + 20}px`,
      height: `${rect.height + 20}px`,
    };

    tooltipStyle.value = {
      top: `${rect.top + scrollTop - 140}px`,
      left: `${rect.left}px`,
    };
  }
};

const handleResize = () => positionHighlight();

onMounted(() => {
  if (!localStorage.getItem('onboarding-completed')) {
    setTimeout(startTour, 500);
  }
  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
});

defineExpose({ startTour });
</script>

<template>
  <div v-if="isVisible" class="fixed inset-0 z-[100] pointer-events-none">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/50" />

    <!-- Highlight -->
    <div
      class="absolute border-2 border-primary rounded-lg transition-all duration-300 pointer-events-none shadow-lg"
      :style="highlightStyle"
    />

    <!-- Tooltip -->
    <Card
      v-if="steps[currentStep]"
      class="absolute w-64 pointer-events-auto shadow-xl border-primary/20 z-[101]"
      :style="tooltipStyle"
    >
      <CardHeader class="pb-2">
        <CardTitle class="flex items-center gap-2 text-sm">
          <component :is="steps[currentStep].icon" class="h-4 w-4 text-primary" />
          {{ steps[currentStep].title }}
        </CardTitle>
      </CardHeader>
      <CardContent class="pt-0">
        <p class="text-sm text-muted-foreground mb-3">
          {{ steps[currentStep].content }}
        </p>
        <div class="flex justify-between items-center">
          <span class="text-xs text-muted-foreground">
            {{ currentStep + 1 }}/{{ steps.length }}
          </span>
          <div class="flex gap-2">
            <Button variant="ghost" size="sm" @click="finishTour">Saltar</Button>
            <Button
              v-if="currentStep > 0"
              variant="outline"
              size="sm"
              @click="prevStep"
            >
              Anterior
            </Button>
            <Button size="sm" @click="nextStep">
              {{ currentStep === steps.length - 1 ? 'Finalizar' : 'Siguiente' }}
            </Button>
          </div>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
