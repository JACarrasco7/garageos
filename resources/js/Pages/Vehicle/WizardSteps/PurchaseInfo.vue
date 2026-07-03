<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Input from '@/Components/ui/input/Input.vue';
import Label from '@/Components/ui/label/Label.vue';

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
      <h3 class="text-lg font-semibold text-foreground">Datos de compra</h3>
      <p class="text-sm text-muted-foreground">
        Esta información es opcional pero ayuda a calcular el coste por km y el valor de mercado.
      </p>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
      <div class="space-y-2">
        <Label for="purchase_date">Fecha de compra</Label>
        <Input
          id="purchase_date"
          v-model="form.purchase_date"
          type="date"
          :class="{ 'border-destructive': form.errors.purchase_date }"
        />
        <p v-if="form.errors.purchase_date" class="text-sm text-destructive">
          {{ form.errors.purchase_date }}
        </p>
      </div>

      <div class="space-y-2">
        <Label for="purchase_price">Precio de compra (€)</Label>
        <Input
          id="purchase_price"
          v-model.number="form.purchase_price"
          type="number"
          placeholder="Ej: 15000"
          min="0"
          step="0.01"
          :class="{ 'border-destructive': form.errors.purchase_price }"
        />
        <p v-if="form.errors.purchase_price" class="text-sm text-destructive">
          {{ form.errors.purchase_price }}
        </p>
      </div>
    </div>

    <div class="rounded-xl border border-border bg-muted/30 p-4">
      <h4 class="font-medium text-foreground mb-2">¿Por qué incluir estos datos?</h4>
      <ul class="space-y-2 text-sm text-muted-foreground">
        <li class="flex items-start gap-2">
          <span class="text-primary">•</span>
          <span>Calcular el coste de mantenimiento por km</span>
        </li>
        <li class="flex items-start gap-2">
          <span class="text-primary">•</span>
          <span>Seguimiento de valorización del vehículo</span>
        </li>
        <li class="flex items-start gap-2">
          <span class="text-primary">•</span>
          <span>Generación de informes de venta</span>
        </li>
      </ul>
    </div>
  </div>
</template>
