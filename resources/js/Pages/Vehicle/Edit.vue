<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

interface Garage {
  id: number
  name: string
}

interface Vehicle {
  id: number
  plate: string
  vin: string | null
  brand: string
  model: string
  year: number
  fuel_type: string
  color: string | null
  current_km: number
  garage_id: number
  specs?: {
    engine_cc: number | null
    power_hp: number | null
    transmission: string | null
  } | null
}

const props = defineProps<{
  vehicle: Vehicle
  garages: Garage[]
}>()

const form = useForm({
  garage_id: props.vehicle.garage_id,
  plate: props.vehicle.plate,
  vin: props.vehicle.vin || '',
  brand: props.vehicle.brand,
  model: props.vehicle.model,
  year: String(props.vehicle.year),
  fuel_type: props.vehicle.fuel_type,
  color: props.vehicle.color || '',
  current_km: String(props.vehicle.current_km),
  engine_cc: String(props.vehicle.specs?.engine_cc || ''),
  power_hp: String(props.vehicle.specs?.power_hp || ''),
  transmission: props.vehicle.specs?.transmission || '',
})

const submit = () => {
  form.put(route('vehicles.update', props.vehicle.id))
}
</script>

<template>
  <Head title="Editar Vehículo" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight">Editar Vehículo</h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <InputLabel for="brand" value="Marca" />
                <TextInput
                  id="brand"
                  v-model="form.brand"
                  type="text"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.brand" class="mt-2" />
              </div>

              <div>
                <InputLabel for="model" value="Modelo" />
                <TextInput
                  id="model"
                  v-model="form.model"
                  type="text"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.model" class="mt-2" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <InputLabel for="plate" value="Matrícula" />
                <TextInput
                  id="plate"
                  v-model="form.plate"
                  type="text"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.plate" class="mt-2" />
              </div>

              <div>
                <InputLabel for="year" value="Año" />
                <TextInput
                  id="year"
                  v-model="form.year"
                  type="number"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.year" class="mt-2" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <InputLabel for="fuel_type" value="Combustible" />
                <select
                  id="fuel_type"
                  v-model="form.fuel_type"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md"
                >
                  <option value="gasolina">Gasolina</option>
                  <option value="diesel">Diesel</option>
                  <option value="hibrido">Híbrido</option>
                  <option value="electrico">Eléctrico</option>
                  <option value="glp">GLP</option>
                </select>
                <InputError :message="form.errors.fuel_type" class="mt-2" />
              </div>

              <div>
                <InputLabel for="current_km" value="Km actuales" />
                <TextInput
                  id="current_km"
                  v-model="form.current_km"
                  type="number"
                  class="mt-1 block w-full"
                  required
                />
                <InputError :message="form.errors.current_km" class="mt-2" />
              </div>
            </div>

            <div>
              <h3 class="text-lg font-medium mb-3">Especificaciones</h3>
              <div class="grid grid-cols-3 gap-4">
                <div>
                  <InputLabel for="engine_cc" value="Cilindrada (cc)" />
                  <TextInput
                    id="engine_cc"
                    v-model="form.engine_cc"
                    type="number"
                    class="mt-1 block w-full"
                  />
                </div>

                <div>
                  <InputLabel for="power_hp" value="Potencia (CV)" />
                  <TextInput
                    id="power_hp"
                    v-model="form.power_hp"
                    type="number"
                    class="mt-1 block w-full"
                  />
                </div>

                <div>
                  <InputLabel for="transmission" value="Transmisión" />
                  <select
                    id="transmission"
                    v-model="form.transmission"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md"
                  >
                    <option value="">Seleccionar</option>
                    <option value="manual">Manual</option>
                    <option value="automatic">Automático</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="flex justify-end space-x-3">
              <Link
                :href="route('vehicles.show', props.vehicle.id)"
                class="px-4 py-2 text-gray-600 hover:underline"
              >
                Cancelar
              </Link>
              <PrimaryButton :disabled="form.processing">
                {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

