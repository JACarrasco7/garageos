<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/lib/utils';

interface Photo {
  id: number;
  url: string;
  thumbnail_url: string;
  category: string;
  caption: string | null;
}

const props = defineProps<{
  photos: Photo[];
  category?: string;
}>();

const emit = defineEmits<{
  (e: 'photoClick', photo: Photo): void;
  (e: 'delete', photoId: number): void;
}>();

const categoryColors: Record<string, string> = {
  principal: 'ring-green-500',
  frontal: 'ring-blue-500',
  lateral: 'ring-cyan-500',
  trasero: 'ring-purple-500',
  interior: 'ring-yellow-500',
  motor: 'ring-red-500',
  averia: 'ring-orange-500',
  daño: 'ring-pink-500',
  documento: 'ring-indigo-500',
  antes_reparacion: 'ring-slate-500',
  despues_reparacion: 'ring-teal-500',
};

const categoryLabels: Record<string, string> = {
  principal: 'Principal',
  frontal: 'Frente',
  lateral: 'Lateral',
  trasero: 'Atrás',
  interior: 'Interior',
  motor: 'Motor',
  averia: 'Avería',
  daño: 'Daño',
  documento: 'Documento',
  antes_reparacion: 'Antes',
  despues_reparacion: 'Después',
};

const displayPhotos = computed(() => {
  if (!props.category || props.category === 'all') return props.photos;
  return props.photos.filter((p) => p.category === props.category);
});
</script>

<template>
  <div v-if="displayPhotos.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    <div
      v-for="photo in displayPhotos"
      :key="photo.id"
      class="group relative aspect-square rounded-lg overflow-hidden cursor-pointer transition-transform hover:scale-105"
      :class="cn('ring-2', categoryColors[photo.category] || 'ring-gray-300')"
      @click="emit('photoClick', photo)"
    >
      <img
        :src="photo.thumbnail_url || photo.url"
        :alt="photo.caption || 'Foto'"
        class="w-full h-full object-cover"
        loading="lazy"
      />

      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
        <div class="absolute bottom-2 left-2 right-2">
          <p v-if="photo.caption" class="text-white text-xs font-medium truncate">
            {{ photo.caption }}
          </p>
          <p class="text-white/70 text-xs capitalize">
            {{ categoryLabels[photo.category] || photo.category }}
          </p>
        </div>
      </div>

      <button
        type="button"
        class="absolute top-2 right-2 p-1 rounded-full bg-black/50 text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/70"
        @click.stop="emit('delete', photo.id)"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </div>

  <div v-else class="flex flex-col items-center justify-center py-12 text-center">
    <svg class="w-16 h-16 text-muted-foreground/50 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.5-4.5L9 14l4-4 4 4 3.5-3.5L20 8m0 8V8m0 8l-4-4-4 4-4-4-4.5 4.5" />
    </svg>
    <p class="text-lg font-medium text-muted-foreground">No hay fotos</p>
    <p class="text-sm text-muted-foreground mt-1">Añade la primera foto usando el botón de arriba</p>
  </div>
</template>
