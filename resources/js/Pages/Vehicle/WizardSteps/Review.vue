<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import PageCard from '@/Components/PageCard.vue';
import CardHeader from '@/Components/ui/card/CardHeader.vue';
import CardTitle from '@/Components/ui/card/CardTitle.vue';
import CardContent from '@/Components/ui/card/CardContent.vue';
import Button from '@/Components/ui/button/Button.vue';
import { CheckCircle, Car, Settings, Calendar, DollarSign } from 'lucide-vue-next';

// Define URL type globally
declare const URL: {
  createObjectURL(file: File): string;
};

const props = defineProps<{
    modelValue: ReturnType<typeof useForm>;
}>();

const emit = defineEmits<{
    (e: 'submit'): void;
}>();

const form = computed(() => props.modelValue);

const fuelTypeLabels: Record<string, string> = {
    gasolina: 'Gasolina',
    diesel: 'Diésel',
    hibrido: 'Híbrido',
    electrico: 'Eléctrico',
    glp: 'GLP',
};

const transmissionLabels: Record<string, string> = {
    manual: 'Manual',
    automatico: 'Automático',
    cvt: 'CVT',
};

const driveLabels: Record<string, string> = {
    fwd: 'Delantera',
    rwd: 'Trasera',
    '4wd': '4x4',
    awd: 'AWD',
};

const formatDate = (date: string | null) => {
    if (!date) return 'No especificado';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
};

const formatPrice = (price: number | null) => {
    if (!price) return 'No especificado';
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
    }).format(price);
};

const submit = () => {
    emit('submit');
};
</script>

<template>
  <div class="space-y-6">
    <div class="space-y-4">
      <h3 class="text-lg font-semibold text-foreground">Revisa la información</h3>
      <p class="text-sm text-muted-foreground">
        Verifica que todos los datos son correctos antes de crear el vehículo.
      </p>
    </div>

    <div class="grid gap-4">
      <PageCard>
        <template #title>
          <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-base">
              <Car class="h-4 w-4 text-primary" />
              Información básica
            </CardTitle>
          </CardHeader>
        </template>
        <CardContent class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <p class="text-muted-foreground">Matrícula</p>
            <p class="font-medium text-foreground">{{ form.plate }}</p>
          </div>
          <div v-if="form.vin">
            <p class="text-muted-foreground">Bastidor (VIN)</p>
            <p class="font-medium text-foreground">{{ form.vin }}</p>
          </div>
          <div>
            <p class="text-muted-foreground">Marca</p>
            <p class="font-medium text-foreground">{{ form.brand }}</p>
          </div>
          <div>
            <p class="text-muted-foreground">Modelo</p>
            <p class="font-medium text-foreground">{{ form.model }}</p>
          </div>
          <div>
            <p class="text-muted-foreground">Año</p>
            <p class="font-medium text-foreground">{{ form.year }}</p>
          </div>
          <div>
            <p class="text-muted-foreground">Color</p>
            <p class="font-medium text-foreground">{{ form.color || 'No especificado' }}</p>
          </div>
          <div>
            <p class="text-muted-foreground">Matriculado</p>
            <p class="font-medium text-foreground">{{ formatDate(form.registration_date) }}</p>
          </div>
          <div>
            <p class="text-muted-foreground">Kilometraje</p>
            <p class="font-medium text-foreground">{{ form.current_km.toLocaleString() }} km</p>
          </div>
        </CardContent>
      </PageCard>

      <PageCard v-if="form.engine_cc || form.power_hp || form.transmission || form.drive">
        <template #title>
          <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-base">
              <Settings class="h-4 w-4 text-primary" />
              Ficha técnica
            </CardTitle>
          </CardHeader>
        </template>
        <CardContent class="grid grid-cols-2 gap-4 text-sm">
          <div v-if="form.engine_cc">
            <p class="text-muted-foreground">Cilindrada</p>
            <p class="font-medium text-foreground">{{ form.engine_cc }} CC</p>
          </div>
          <div v-if="form.power_hp">
            <p class="text-muted-foreground">Potencia</p>
            <p class="font-medium text-foreground">{{ form.power_hp }} CV</p>
          </div>
          <div v-if="form.torque_nm">
            <p class="text-muted-foreground">Par motor</p>
            <p class="font-medium text-foreground">{{ form.torque_nm }} Nm</p>
          </div>
          <div v-if="form.transmission">
            <p class="text-muted-foreground">Transmisión</p>
            <p class="font-medium text-foreground">{{ transmissionLabels[form.transmission] }}</p>
          </div>
          <div v-if="form.drive">
            <p class="text-muted-foreground">Tracción</p>
            <p class="font-medium text-foreground">{{ driveLabels[form.drive] }}</p>
          </div>
          <div v-if="form.doors">
            <p class="text-muted-foreground">Puertas</p>
            <p class="font-medium text-foreground">{{ form.doors }}</p>
          </div>
          <div v-if="form.seats">
            <p class="text-muted-foreground">Asientos</p>
            <p class="font-medium text-foreground">{{ form.seats }}</p>
          </div>
        </CardContent>
      </PageCard>

      <PageCard v-if="form.purchase_date || form.purchase_price">
        <template #title>
          <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-base">
              <DollarSign class="h-4 w-4 text-primary" />
              Datos de compra
            </CardTitle>
          </CardHeader>
        </template>
        <CardContent class="grid grid-cols-2 gap-4 text-sm">
          <div v-if="form.purchase_date">
            <p class="text-muted-foreground">Fecha de compra</p>
            <p class="font-medium text-foreground">{{ formatDate(form.purchase_date) }}</p>
          </div>
          <div v-if="form.purchase_price">
            <p class="text-muted-foreground">Precio</p>
            <p class="font-medium text-foreground">{{ formatPrice(form.purchase_price) }}</p>
          </div>
        </CardContent>
      </PageCard>

      <PageCard v-if="form.photo">
        <template #title>
          <CardHeader class="pb-3">
            <CardTitle class="text-base">Foto del vehículo</CardTitle>
          </CardHeader>
        </template>
        <CardContent>
          <img
            :src="URL.createObjectURL(form.photo as File)"
            alt="Vista previa"
            class="h-48 w-full rounded-lg object-cover"
          />
        </CardContent>
      </PageCard>
    </div>

    <div class="flex items-start gap-3 rounded-xl bg-primary/5 p-4">
      <CheckCircle class="h-5 w-5 text-primary mt-0.5" />
      <div class="space-y-1">
        <p class="text-sm font-medium text-foreground">
          ¿Todo correcto?
        </p>
        <p class="text-xs text-muted-foreground">
          Una vez creado, puedes editar todos estos datos en cualquier momento.
        </p>
      </div>
    </div>

    <Button
      size="lg"
      class="w-full"
      :disabled="form.processing"
      @click="submit"
    >
      <CheckCircle v-if="!form.processing" class="mr-2 h-4 w-4" />
      {{ form.processing ? 'Creando vehículo...' : 'Crear vehículo' }}
    </Button>
  </div>
</template>
