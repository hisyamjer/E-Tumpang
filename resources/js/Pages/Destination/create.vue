<script setup>
import { onMounted, ref } from 'vue'; 
import appLayout from '@/Layouts/sidebar.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const form = useForm({
  destination: '',
  departure_time: '',
  available_seats: 1,
  status: 'available',
  price: '',
  latitude: 3.1390,
  longitude: 101.6869,
  date: '',
});

// Fix for Leaflet marker icons not showing up in build tools
const fixLeafletIcons = () => {
  delete L.Icon.Default.prototype._getIconUrl;
  L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
  });
};

onMounted(() => {
  fixLeafletIcons();

  // Initialize map ONLY after component is mounted
  const map = L.map('map-container').setView([form.latitude, form.longitude], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(map);

  const marker = L.marker([form.latitude, form.longitude], {
    draggable: true
  }).addTo(map);

  marker.on('dragend', function (event) {
    const position = event.target.getLatLng();
    form.latitude = position.lat;
    form.longitude = position.lng;
    fetchAddress(position.lat, position.lng);
  });
});

async function fetchAddress(lat, lng) {
  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
    const data = await response.json();
    if (data.display_name) {
      form.destination = data.display_name;
    }
  } catch (e) {
    console.error("Mapping service unavailable", e);
  }
}

const submit = () => {
  form.post('/destination');
};
</script>

<template>
  <Head title="New Trip | E-Tumpang" />
  <appLayout>
    <template #header>
      <h1 >New Trip</h1>
    </template>

    <div class="py-12 flex justify-center">
      <Card class="w-full max-w-2xl">
        <CardHeader>
          <CardTitle>Create a new trip</CardTitle>
        </CardHeader>

        <CardContent>
          <form id="trip-form" class="space-y-6" @submit.prevent="submit">
            <!-- Map Section -->
            <div class="space-y-2">
              <Label>Destination (Drag marker to pinpoint)</Label>
              <div id="map-container" class="w-full h-[400px] rounded-lg border z-0"></div>
              <div class="p-3 bg-muted/50 rounded-md text-sm border">
                <p class="font-medium text-primary">{{ form.destination || 'Select a location on the map...' }}</p>
                <p class="text-xs text-muted-foreground mt-1">Lat: {{ form.latitude }}, Lng: {{ form.longitude }}</p>
              </div>
              <Input type="hidden" v-model="form.destination" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label for="departure_time">Departure Time</Label>
                <Input id="departure_time" type="time" v-model="form.departure_time" :error="!!form.errors.departure_time" />
              </div>

              <div class="space-y-2">
                <Label for="available_seats">Available Seats</Label>
                <Input id="available_seats" type="number" v-model="form.available_seats" />
              </div>
            </div>

            <div class="space-y-2">
              <Label for="price">Price (RM)</Label>
              <Input id="price" type="number" step="0.01" v-model="form.price" placeholder="0.00" />
            </div>

            <div class="space-y-2">
              <Label for="date">Date</Label>
              <Input id="date" type="date" step="0.01" v-model="form.date" />
            </div>
          </form>
        </CardContent>

        <CardFooter class="flex justify-between border-t p-6">
          <Button variant="outline" asChild>
            <Link href="/destination">Cancel</Link>
          </Button>
          <Button @click="submit" :disabled="form.processing">
            {{ form.processing ? 'Creating...' : 'Create Trip' }}
          </Button>
        </CardFooter>
      </Card>
    </div>
  </appLayout>
</template>

<style scoped>
/* Ensure leaflet doesn't conflict with sidebar/layouts */
#map-container {
  overflow: hidden;
}
</style>