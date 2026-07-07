<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'

interface Workshop {
  id: number
  name: string
  address: string
  city: string
  lat: number
  lng: number
  rating: number
  distance_km?: number
}

interface Props {
  workshops: Workshop[]
  centerLat?: number
  centerLng?: number
  zoom?: number
}

const props = withDefaults(defineProps<Props>(), {
  centerLat: 40.4168,
  centerLng: -3.7038,
  zoom: 12,
})

const mapRef = ref<HTMLElement | null>(null)
let map: L.Map | null = null
let markers: L.Marker[] = []

const icon = L.icon({
  iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
  shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
})

function initMap() {
  if (!mapRef.value) return

  map = L.map(mapRef.value).setView([props.centerLat, props.centerLng], props.zoom)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(map)
}

function updateMarkers() {
  markers.forEach(m => map?.removeLayer(m))
  markers = []

  props.workshops.forEach(w => {
    const marker = L.marker([w.lat, w.lng], { icon }).addTo(map!)
    const popup = `
      <strong>${w.name}</strong><br/>
      ${w.address}<br/>
      ${w.city}<br/>
      ⭐ ${w.rating || 0}
      ${w.distance_km ? `<br/>📍 ${w.distance_km} km` : ''}
    `
    marker.bindPopup(popup)
    markers.push(marker)
  })
}

onMounted(() => {
  initMap()
  setTimeout(updateMarkers, 100)
})

onUnmounted(() => {
  if (map) {
    map.remove()
    map = null
  }
})

defineExpose({ refresh: updateMarkers })
</script>

<template>
  <div ref="mapRef" class="h-96 w-full rounded-lg"></div>
</template>
