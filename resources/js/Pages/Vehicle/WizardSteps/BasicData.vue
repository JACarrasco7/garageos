<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Input from '@/Components/ui/input/Input.vue';
import Label from '@/Components/ui/label/Label.vue';
import Select from '@/Components/ui/select/Select.vue';
import SelectTrigger from '@/Components/ui/select/SelectTrigger.vue';
import SelectValue from '@/Components/ui/select/SelectValue.vue';
import SelectContent from '@/Components/ui/select/SelectContent.vue';
import SelectItem from '@/Components/ui/select/SelectItem.vue';
import Button from '@/Components/ui/button/Button.vue';
import { Loader2, RefreshCw } from 'lucide-vue-next';
import { cn } from '@/lib/utils';

const props = defineProps<{
    modelValue: ReturnType<typeof useForm>;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: typeof props.modelValue): void;
}>();

const form = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const brands = ref<Array<{ id: number; name: string }>>([]);
const models = ref<Array<{ id: number; name: string }>>([]);
const loadingBrands = ref(false);
const loadingModels = ref(false);
const vinDecoded = ref(false);

const fetchBrands = async () => {
    loadingBrands.value = true;
    try {
        const response = await fetch('/api/vehicles/brands');
        brands.value = await response.json();
    } catch (error) {
        console.error('Error fetching brands:', error);
    } finally {
        loadingBrands.value = false;
    }
};

const fetchModels = async () => {
    if (!form.value.brand || !form.value.year) return;

    loadingModels.value = true;
    try {
        const response = await fetch(`/api/vehicles/models/${form.value.brand}/${form.value.year}`);
        models.value = await response.json();
    } catch (error) {
        console.error('Error fetching models:', error);
    } finally {
        loadingModels.value = false;
    }
};

const decodeVin = async () => {
    if (!form.value.vin || form.value.vin.length !== 17) return;

    try {
        const response = await fetch(`/api/vehicles/decode-vin/${form.value.vin}`);
        const data = await response.json();

        if (data) {
            form.value.brand = data.brand || '';
            form.value.model = data.model || '';
            form.value.year = data.year || new Date().getFullYear();
            form.value.engine_cc = data.engine_cc || null;
            form.value.power_hp = data.power_hp || null;
            form.value.transmission = data.transmission || null;
            form.value.drive = data.drive || null;
            form.value.doors = data.doors || null;
            form.value.seats = data.seats || null;
            vinDecoded.value = true;

            await fetchModels();

            if (data.brand && data.model && data.year) {
                try {
                    const spanishResponse = await fetch(`/api/vehicles/spanish-specs/${data.brand}/${data.model}/${data.year}`);
                    const spanishData = await spanishResponse.json();

                    if (spanishData) {
                        form.value.emissions_co2 = spanishData.emissions_co2 || null;
                        form.value.official_consumption = spanishData.official_consumption || null;
                        form.value.eco_label = spanishData.eco_label || null;
                    }
                } catch (error) {
                    console.error('Error fetching Spanish specs:', error);
                }
            }
        }
    } catch (error) {
        console.error('Error decoding VIN:', error);
    }
};

watch(() => form.value.brand, fetchModels);
watch(() => form.value.year, fetchModels);

fetchBrands();
</script>

<template>
  <div class="space-y-6">
    <div class="space-y-4">
      <h3 class="text-lg font-semibold text-foreground">Información del vehículo</h3>
      <p class="text-sm text-muted-foreground">
        Introduce los datos básicos de tu vehículo. Si tienes el número de bastidor, podemos autocompletar muchos campos.
      </p>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
      <div class="space-y-2">
        <Label for="plate">Matrícula *</Label>
        <Input
          id="plate"
          v-model="form.plate"
          placeholder="1234 ABC"
          :class="{ 'border-destructive': form.errors.plate }"
        />
        <p v-if="form.errors.plate" class="text-sm text-destructive">
          {{ form.errors.plate }}
        </p>
      </div>

      <div class="space-y-2">
        <Label for="vin">Número de bastidor (VIN)</Label>
        <div class="flex gap-2">
          <Input
            id="vin"
            v-model="form.vin"
            placeholder="WBA12345678901234"
            maxlength="17"
            :class="{ 'border-destructive': form.errors.vin }"
            @blur="decodeVin"
          />
          <Button
            variant="outline"
            size="icon"
            :disabled="!form.vin || form.vin.length !== 17 || loadingModels"
            @click="decodeVin"
          >
            <RefreshCw :class="{ 'animate-spin': loadingModels }" class="h-4 w-4" />
          </Button>
        </div>
        <p v-if="form.errors.vin" class="text-sm text-destructive">
          {{ form.errors.vin }}
        </p>
        <p v-if="vinDecoded" class="text-sm text-primary">
          ✓ VIN decodificado correctamente
        </p>
      </div>

      <div class="space-y-2">
        <Label for="brand">Marca *</Label>
        <Select v-model="form.brand" :disabled="loadingBrands">
          <SelectTrigger :class="{ 'border-destructive': form.errors.brand }">
            <SelectValue placeholder="Selecciona marca" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="custom">Otra (especificar abajo)</SelectItem>
            <SelectItem
              v-for="brand in brands"
              :key="brand.id"
              :value="brand.name"
            >
              {{ brand.name }}
            </SelectItem>
          </SelectContent>
        </Select>
        <p v-if="form.errors.brand" class="text-sm text-destructive">
          {{ form.errors.brand }}
        </p>
      </div>

      <div v-if="form.brand === 'custom'" class="space-y-2">
        <Label for="brand-custom">Marca (personalizada)</Label>
        <Input
          id="brand-custom"
          v-model="form.brand"
          placeholder="Ej: Toyota"
        />
      </div>

      <div class="space-y-2">
        <Label for="model">Modelo *</Label>
        <Select v-model="form.model" :disabled="!form.brand || loadingModels">
          <SelectTrigger :class="{ 'border-destructive': form.errors.model }">
            <SelectValue placeholder="Selecciona modelo" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="custom">Otro (especificar abajo)</SelectItem>
            <SelectItem
              v-for="model in models"
              :key="model.id"
              :value="model.name"
            >
              {{ model.name }}
            </SelectItem>
          </SelectContent>
        </Select>
        <p v-if="form.errors.model" class="text-sm text-destructive">
          {{ form.errors.model }}
        </p>
      </div>

      <div v-if="form.model === 'custom'" class="space-y-2">
        <Label for="model-custom">Modelo (personalizado)</Label>
        <Input
          id="model-custom"
          v-model="form.model"
          placeholder="Ej: Corolla"
        />
      </div>

      <div class="space-y-2">
        <Label for="year">Año *</Label>
        <Select v-model="form.year">
          <SelectTrigger :class="{ 'border-destructive': form.errors.year }">
            <SelectValue placeholder="Selecciona año" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem
              v-for="year in Array.from({ length: 30 }, (_, i) => new Date().getFullYear() - i)"
              :key="year"
              :value="year"
            >
              {{ year }}
            </SelectItem>
          </SelectContent>
        </Select>
        <p v-if="form.errors.year" class="text-sm text-destructive">
          {{ form.errors.year }}
        </p>
      </div>

      <div class="space-y-2">
        <Label for="registration_date">Fecha de matriculación</Label>
        <Input
          id="registration_date"
          v-model="form.registration_date"
          type="date"
          :class="{ 'border-destructive': form.errors.registration_date }"
        />
        <p v-if="form.errors.registration_date" class="text-sm text-destructive">
          {{ form.errors.registration_date }}
        </p>
      </div>

      <div class="space-y-2">
        <Label for="color">Color</Label>
        <Input
          id="color"
          v-model="form.color"
          placeholder="Ej: Rojo metálico"
        />
      </div>
    </div>
  </div>
</template>
