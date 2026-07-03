<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import Card from '@/Components/ui/card/Card.vue';
import CardHeader from '@/Components/ui/card/CardHeader.vue';
import CardTitle from '@/Components/ui/card/CardTitle.vue';
import CardContent from '@/Components/ui/card/CardContent.vue';
import Button from '@/Components/ui/button/Button.vue';
import BasicData from './WizardSteps/BasicData.vue';
import TechnicalSpecs from './WizardSteps/TechnicalSpecs.vue';
import Photos from './WizardSteps/Photos.vue';
import Documents from './WizardSteps/Documents.vue';
import PurchaseInfo from './WizardSteps/PurchaseInfo.vue';
import Review from './WizardSteps/Review.vue';
import { cn } from '@/lib/utils';

const currentStep = ref(1);
const totalSteps = 6;

const steps = [
    { id: 1, title: 'Datos básicos', icon: 'car' },
    { id: 2, title: 'Ficha técnica', icon: 'settings' },
    { id: 3, title: 'Fotos', icon: 'image' },
    { id: 4, title: 'Documentos', icon: 'file' },
    { id: 5, title: 'Datos de compra', icon: 'shopping-cart' },
    { id: 6, title: 'Revisar y crear', icon: 'check' },
];

const form = useForm({
    garage_id: 1,
    plate: '',
    brand: '',
    model: '',
    year: new Date().getFullYear(),
    vin: '',
    registration_date: null,
    color: '',
    engine_cc: null,
    power_hp: null,
    torque_nm: null,
    transmission: null,
    drive: null,
    doors: null,
    seats: null,
    current_km: 0,
    purchase_date: null,
    purchase_price: null,
    photo: null,
    eco_label: null,
    emissions_co2: null,
    official_consumption: null,
});

const currentStepComponent = computed(() => {
    return {
        1: BasicData,
        2: TechnicalSpecs,
        3: Photos,
        4: Documents,
        5: PurchaseInfo,
        6: Review,
    }[currentStep.value];
});

const canGoNext = computed(() => {
    return currentStep.value < totalSteps;
});

const canGoBack = computed(() => {
    return currentStep.value > 1;
});

const nextStep = () => {
    if (canGoNext.value) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (canGoBack.value) {
        currentStep.value--;
    }
};

const submit = () => {
    const formData = new FormData();

    Object.keys(form).forEach(key => {
        const value = form[key];
        if (key === 'photo' && value instanceof File) {
            formData.append('photo', value);
        } else if (value !== null && value !== undefined) {
            formData.append(key, value);
        }
    });

    form.post(route('vehicles.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            currentStep.value = 1;
        },
    });
};
</script>

<template>
  <Head title="Añadir vehículo" />

  <AppSidebarLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-foreground">Añadir vehículo</h1>
          <p class="text-sm text-muted-foreground">Completa los pasos para registrar tu vehículo</p>
        </div>
        <Button variant="ghost" size="sm" as-child>
          <Link :href="route('vehicles.index')">
            Cancelar
          </Link>
        </Button>
      </div>
    </template>

    <div class="mx-auto max-w-4xl space-y-6">
      <Card class="border backdrop-blur-xl bg-card/80">
        <CardHeader>
          <div class="flex items-center justify-between">
            <CardTitle class="text-lg font-semibold">
              Paso {{ currentStep }} de {{ totalSteps }}
            </CardTitle>
            <span class="text-sm text-muted-foreground">
              {{ steps[currentStep - 1].title }}
            </span>
          </div>
          <div class="mt-4 flex items-center gap-2">
            <div
              v-for="step in steps"
              :key="step.id"
              :class="cn(
                'flex-1 h-1 rounded-full transition-all duration-300',
                step.id <= currentStep ? 'bg-primary' : 'bg-muted'
              )"
            />
          </div>
        </CardHeader>
        <CardContent>
          <component
            :is="currentStepComponent"
            v-model="form"
            @next="nextStep"
            @back="prevStep"
            @submit="submit"
          />
        </CardContent>
      </Card>

      <div class="flex items-center justify-between">
        <Button
          v-if="canGoBack"
          variant="ghost"
          size="lg"
          @click="prevStep"
        >
          Anterior
        </Button>

        <Button
          v-if="canGoNext"
          size="lg"
          class="ml-auto"
          @click="nextStep"
        >
          Siguiente
        </Button>

        <Button
          v-if="currentStep === totalSteps"
          size="lg"
          class="ml-auto"
          :disabled="form.processing"
          @click="submit"
        >
          {{ form.processing ? 'Guardando...' : 'Crear vehículo' }}
        </Button>
      </div>
    </div>
  </AppSidebarLayout>
</template>
