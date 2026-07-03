<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Input from '@/Components/ui/input/Input.vue';
import Label from '@/Components/ui/label/Label.vue';
import Select from '@/Components/ui/select/Select.vue';
import SelectTrigger from '@/Components/ui/select/SelectTrigger.vue';
import SelectValue from '@/Components/ui/select/SelectValue.vue';
import SelectContent from '@/Components/ui/select/SelectContent.vue';
import SelectItem from '@/Components/ui/select/SelectItem.vue';

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
</script>

<template>
  <div class="space-y-6">
    <div class="space-y-4">
      <h3 class="text-lg font-semibold text-foreground">Ficha técnica</h3>
      <p class="text-sm text-muted-foreground">
        Estos campos son opcionales. Si decodificaste el VIN, es probable que ya estén rellenados.
      </p>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
      <div class="space-y-2">
        <Label for="engine_cc">Cilindrada (CC)</Label>
        <Input
          id="engine_cc"
          v-model.number="form.engine_cc"
          type="number"
          placeholder="Ej: 2000"
          min="500"
          max="8000"
        />
      </div>

      <div class="space-y-2">
        <Label for="power_hp">Potencia (CV)</Label>
        <Input
          id="power_hp"
          v-model.number="form.power_hp"
          type="number"
          placeholder="Ej: 150"
          min="30"
          max="1000"
        />
      </div>

      <div class="space-y-2">
        <Label for="torque_nm">Par motor (Nm)</Label>
        <Input
          id="torque_nm"
          v-model.number="form.torque_nm"
          type="number"
          placeholder="Ej: 250"
          min="50"
          max="1500"
        />
      </div>

      <div class="space-y-2">
        <Label for="transmission">Transmisión</Label>
        <Select v-model="form.transmission">
          <SelectTrigger>
            <SelectValue placeholder="Selecciona transmisión" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="manual">Manual</SelectItem>
            <SelectItem value="automatico">Automático</SelectItem>
            <SelectItem value="cvt">CVT</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <div class="space-y-2">
        <Label for="drive">Tracción</Label>
        <Select v-model="form.drive">
          <SelectTrigger>
            <SelectValue placeholder="Selecciona tracción" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="fwd">Delantera (FWD)</SelectItem>
            <SelectItem value="rwd">Trasera (RWD)</SelectItem>
            <SelectItem value="4wd">4x4 (4WD)</SelectItem>
            <SelectItem value="awd">AWD</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <div class="space-y-2">
        <Label for="doors">Puertas</Label>
        <Select v-model="form.doors">
          <SelectTrigger>
            <SelectValue placeholder="Selecciona puertas" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem :value="2">2 puertas</SelectItem>
            <SelectItem :value="3">3 puertas</SelectItem>
            <SelectItem :value="4">4 puertas</SelectItem>
            <SelectItem :value="5">5 puertas</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <div class="space-y-2">
        <Label for="seats">Asientos</Label>
        <Select v-model="form.seats">
          <SelectTrigger>
            <SelectValue placeholder="Selecciona asientos" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem :value="2">2 asientos</SelectItem>
            <SelectItem :value="4">4 asientos</SelectItem>
            <SelectItem :value="5">5 asientos</SelectItem>
            <SelectItem :value="7">7 asientos</SelectItem>
            <SelectItem :value="9">9 asientos</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <div class="col-span-1 md:col-span-2 mt-4 border-t pt-4">
        <h4 class="text-sm font-semibold text-foreground mb-3">Datos obligatorios en España</h4>
      </div>

      <div class="space-y-2">
        <Label for="registration_date">Fecha de matriculación</Label>
        <Input
          id="registration_date"
          v-model="form.registration_date"
          type="date"
          :max="new Date().toISOString().split('T')[0]"
        />
        <p class="text-xs text-muted-foreground">Fecha de primera matriculación en España</p>
      </div>

      <div class="space-y-2">
        <Label for="eco_label">Etiqueta medioambiental</Label>
        <Select v-model="form.eco_label">
          <SelectTrigger>
            <SelectValue placeholder="Selecciona etiqueta" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="ECO">ECO (Verde)</SelectItem>
            <SelectItem value="C">C (Azul)</SelectItem>
            <SelectItem value="B">B (Amarillo)</SelectItem>
            <SelectItem value="Zero">Zero (Azul oscuro)</SelectItem>
          </SelectContent>
        </Select>
        <p class="text-xs text-muted-foreground">Etiqueta de la DGT para restricciones de tráfico</p>
      </div>

      <div class="space-y-2">
        <Label for="emissions_co2">Emisiones CO2 (g/km)</Label>
        <Input
          id="emissions_co2"
          v-model.number="form.emissions_co2"
          type="number"
          placeholder="Ej: 120"
          min="0"
          max="500"
        />
        <p class="text-xs text-muted-foreground">Emisiones oficiales (necesario para impuestos)</p>
      </div>

      <div class="space-y-2">
        <Label for="official_consumption">Consumo oficial (l/100km)</Label>
        <Input
          id="official_consumption"
          v-model.number="form.official_consumption"
          type="number"
          placeholder="Ej: 5.5"
          min="0"
          max="20"
          step="0.1"
        />
        <p class="text-xs text-muted-foreground">Consumo homologado WLTP</p>
      </div>

      <div class="space-y-2">
        <Label for="current_km">Kilometraje actual *</Label>
        <Input
          id="current_km"
          v-model.number="form.current_km"
          type="number"
          placeholder="Ej: 50000"
          min="0"
          :class="{ 'border-destructive': form.errors.current_km }"
        />
        <p v-if="form.errors.current_km" class="text-sm text-destructive">
          {{ form.errors.current_km }}
        </p>
      </div>
    </div>
  </div>
</template>
