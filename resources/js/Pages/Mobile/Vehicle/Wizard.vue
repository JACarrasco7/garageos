<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import AppMobileLayout from '@/layouts/mobile/AppMobileLayout.vue';
import MobileHeader from '@/Components/mobile/MobileHeader.vue';
import MobileCard from '@/Components/mobile/MobileCard.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { cn } from '@/lib/utils';
import { RefreshCw } from 'lucide-vue-next';

interface Garage {
  id: number;
  name: string;
}

const props = defineProps<{
  garages: Garage[];
}>();

const currentStep = ref(1);
const totalSteps = 4;
const vinDecoding = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

const steps = [
  { id: 1, title: 'Datos', icon: 'car' },
  { id: 2, title: 'Especificaciones', icon: 'settings' },
  { id: 3, title: 'Fotos', icon: 'image' },
  { id: 4, title: 'Revisar', icon: 'check' },
];

const form = useForm({
  garage_id: props.garages[0]?.id || 1,
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
  fuel_type: 'gasolina',
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

const canGoNext = computed(() => currentStep.value < totalSteps);
const canGoBack = computed(() => currentStep.value > 1);

const nextStep = () => {
  if (canGoNext.value) currentStep.value++;
};

const prevStep = () => {
  if (canGoBack.value) currentStep.value--;
};

const decodeVin = async () => {
  if (!form.vin || form.vin.length !== 17) return;

  vinDecoding.value = true;
  try {
    const response = await fetch(`/api/vehicles/decode-vin/${form.vin}`);
    const data = await response.json();

    if (data) {
      form.brand = data.brand || '';
      form.model = data.model || '';
      form.year = data.year || new Date().getFullYear();
      form.engine_cc = data.engine_cc || null;
      form.power_hp = data.power_hp || null;
      form.transmission = data.transmission || null;
      form.drive = data.drive || null;
      form.doors = data.doors || null;
      form.seats = data.seats || null;

      if (data.brand && data.model && data.year) {
        try {
          const spanishResponse = await fetch(`/api/vehicles/spanish-specs/${data.brand}/${data.model}/${data.year}`);
          const spanishData = await spanishResponse.json();

          if (spanishData) {
            form.emissions_co2 = spanishData.emissions_co2 || null;
            form.official_consumption = spanishData.official_consumption || null;
            form.eco_label = spanishData.eco_label || null;
          }
        } catch (e) {
          console.error('Spanish specs error:', e);
        }
      }
    }
  } catch (error) {
    console.error('VIN decode error:', error);
  } finally {
    vinDecoding.value = false;
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

  <AppMobileLayout>
    <MobileHeader title="Añadir vehículo" :show-back="true" @back="router.visit(route('mobile.vehicles.index'))" />

    <div class="pb-24">
      <!-- Progress -->
      <div class="px-4 py-3 bg-background border-b">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm text-muted-foreground">Paso {{ currentStep }}/{{ totalSteps }}</span>
          <span class="text-sm font-medium">{{ steps[currentStep - 1].title }}</span>
        </div>
        <div class="flex gap-1">
          <div
            v-for="step in steps"
            :key="step.id"
            :class="cn(
              'flex-1 h-1 rounded-full',
              step.id <= currentStep ? 'bg-primary' : 'bg-muted'
            )"
          />
        </div>
      </div>

      <!-- Steps -->
      <div class="p-4">
        <!-- Step 1: Basic Data -->
        <div v-show="currentStep === 1" class="space-y-4">
          <MobileCard class="p-4">
            <div class="space-y-4">
              <div>
                <Label for="plate">Matrícula</Label>
                <Input id="plate" v-model="form.plate" placeholder="1234ABC" required />
              </div>
              <div>
                <Label for="vin">VIN (opcional)</Label>
                <div class="flex gap-2">
                  <Input id="vin" v-model="form.vin" placeholder="WAUZZZ..." class="flex-1" />
                  <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="decodeVin"
                    :disabled="vinDecoding || form.vin.length !== 17"
                  >
                    <RefreshCw v-if="vinDecoding" class="h-4 w-4 animate-spin" />
                    <span v-else>Decodificar</span>
                  </Button>
                </div>
              </div>
              <div>
                <Label for="brand">Marca</Label>
                <Input id="brand" v-model="form.brand" placeholder="Seat" required />
              </div>
              <div>
                <Label for="model">Modelo</Label>
                <Input id="model" v-model="form.model" placeholder="León" required />
              </div>
              <div>
                <Label for="year">Año</Label>
                <Input id="year" v-model.number="form.year" type="number" :min="1900" :max="new Date().getFullYear() + 1" required />
              </div>
              <div>
                <Label for="current_km">Km actuales</Label>
                <Input id="current_km" v-model.number="form.current_km" type="number" min="0" required />
              </div>
            </div>
          </MobileCard>
        </div>

        <!-- Step 2: Technical Specs -->
        <div v-show="currentStep === 2" class="space-y-4">
          <MobileCard class="p-4">
            <div class="space-y-4">
              <div>
                <Label for="engine_cc">Cilindrada (cc)</Label>
                <Input id="engine_cc" v-model.number="form.engine_cc" type="number" placeholder="2000" />
              </div>
              <div>
                <Label for="power_hp">Potencia (CV)</Label>
                <Input id="power_hp" v-model.number="form.power_hp" type="number" placeholder="150" />
              </div>
              <div>
                <Label for="fuel_type">Combustible</Label>
                <select v-model="form.fuel_type" class="w-full mt-1 px-3 py-2 border rounded-md bg-background">
                  <option value="gasolina">Gasolina</option>
                  <option value="diesel">Diésel</option>
                  <option value="hibrido">Híbrido</option>
                  <option value="electrico">Eléctrico</option>
                  <option value="glp">GLP</option>
                </select>
              </div>
              <div v-if="form.emissions_co2 || form.eco_label">
                <Label>Emisiones CO2</Label>
                <p class="text-sm font-medium">{{ form.emissions_co2 }} g/km</p>
                <Label class="mt-2">Etiqueta ECO</Label>
                <p class="text-sm font-medium">{{ form.eco_label || '-' }}</p>
              </div>
            </div>
          </MobileCard>
        </div>

        <!-- Step 3: Photos -->
        <div v-show="currentStep === 3" class="space-y-4">
          <MobileCard class="p-4">
            <div class="space-y-4">
              <Label>Foto principal (opcional)</Label>
              <input
                type="file"
                accept="image/*"
                @change="e => form.photo = (e.target as HTMLInputElement).files?.[0]"
                class="hidden"
                ref="fileInput"
              />
              <Button type="button" variant="outline" @click="fileInput?.click()" class="w-full">
                Seleccionar foto
              </Button>
              <p v-if="form.photo" class="text-sm text-muted-foreground">
                {{ (form.photo as File).name }}
              </p>
            </div>
          </MobileCard>
        </div>

        <!-- Step 4: Review -->
        <div v-show="currentStep === 4" class="space-y-4">
          <MobileCard class="p-4">
            <h3 class="font-semibold mb-3">Resumen</h3>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-muted-foreground">Matrícula</span>
                <span class="font-medium">{{ form.plate || '-' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-muted-foreground">Marca/Modelo</span>
                <span class="font-medium">{{ form.brand }} {{ form.model }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-muted-foreground">Año</span>
                <span class="font-medium">{{ form.year }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-muted-foreground">Km</span>
                <span class="font-medium">{{ form.current_km }}</span>
              </div>
            </div>
          </MobileCard>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <div class="fixed bottom-0 left-0 right-0 bg-background border-t p-4 safe-area-bottom">
      <div class="flex gap-2">
        <Button
          v-if="canGoBack"
          type="button"
          variant="outline"
          @click="prevStep"
          class="flex-1"
        >
          Atrás
        </Button>
        <Button
          v-if="canGoNext"
          type="button"
          @click="nextStep"
          class="flex-1"
        >
          Siguiente
        </Button>
        <Button
          v-if="!canGoNext"
          type="submit"
          @click="submit"
          class="flex-1"
          :disabled="form.processing"
        >
          Crear vehículo
        </Button>
      </div>
    </div>
  </AppMobileLayout>
</template>
