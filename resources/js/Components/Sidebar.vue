<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Home, Truck, FileText, Wrench, BarChart3, Settings } from 'lucide-vue-next'

const navigation = [
  { name: 'Dashboard', href: route('dashboard'), icon: Home },
  { name: 'Vehículos', href: route('vehicles.index'), icon: Truck },
  { name: 'Documentos', href: route('documents.index'), icon: FileText },
  { name: 'Mantenimiento', href: route('maintenance.index'), icon: Wrench },
  { name: 'Estadísticas', href: '#', icon: BarChart3 },
  { name: 'Configuración', href: route('profile.edit'), icon: Settings },
]
</script>

<template>
  <nav class="w-64 bg-white dark:bg-gray-800 h-screen p-4 overflow-y-auto">
    <div class="mb-8">
      <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">GarageOS</h1>
    </div>

    <ul class="space-y-2">
      <li v-for="item in navigation" :key="item.name">
        <Link
          :href="item.href"
          class="flex items-center gap-3 px-3 py-2 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
        >
          <component :is="item.icon" class="w-5 h-5" />
          {{ item.name }}
        </Link>
      </li>
    </ul>
  </nav>
</template>
