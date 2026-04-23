<script setup>
import { onMounted } from 'vue'; 
import appLayout from '@/Layouts/sidebar.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { MapPin, Car, ChevronLeft } from 'lucide-vue-next';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
  auth: Object
});

const form = useForm({
  destination: '',
  departure_at: '',
  available_seats: 4,
  status: 'available',
  price: '',
  latitude: 3.1390,
  longitude: 101.6869,
  gender_pref: 'Mixed',
  description: '',
  car_model: props.auth.user.model || '', 
  plate_number: props.auth.user.plate_number || '',
});

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
  const map = L.map('map-container').setView([form.latitude, form.longitude], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(map);

  const marker = L.marker([form.latitude, form.longitude], { draggable: true }).addTo(map);

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
    if (data.display_name) { form.destination = data.display_name; }
  } catch (e) { console.error("Mapping service unavailable", e); }
}

const submit = () => { form.post('/destination'); };
</script>

<template>
  <Head title="New Trip | E-Tumpang" />
  <appLayout>
    <template #header>
      <div class="flex items-center gap-4">
        <Link href="/destination" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
          <ChevronLeft class="h-5 w-5 text-slate-600" />
        </Link>
        <h2 class="">Post a New Trip</h2>
      </div>
    </template>

    <div class="py-8 px-8 max-w-4xl">
      <form @submit.prevent="submit" class="space-y-12">
        
        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-1">Step 1</h3>
            <h4 class="text-lg font-semibold text-slate-900">Route & Location</h4>
            <p class="text-sm text-slate-500 mt-2">Specify where you're heading by dragging the map marker.</p>
          </div>
          
          <div class="md:col-span-2 space-y-4">
            <div id="map-container" class="w-full h-[300px] rounded-lg border border-slate-200 z-0 shadow-sm bg-slate-50"></div>
             <div class="p-4 bg-white rounded-lg border border-slate-200 shadow-sm flex items-start gap-3">
               <MapPin class="h-5 w-5 text-red-500 mt-0.5 shrink-0" />
               <div>
                 <p class="text-sm font-bold text-slate-900">Destination</p>
                 <p class="text-sm text-slate-600">{{ form.destination || 'Select a point on the map' }}</p>
               </div>
             </div>

             <div class="space-y-2">
               <Label class="text-slate-700">Destination (Manual)</Label>
               <Input v-model="form.destination" placeholder="Type destination if map lookup fails" />
               <p v-if="form.errors.destination" class="text-xs text-destructive">{{ form.errors.destination }}</p>
             </div>
           </div>
         </section>

        <hr class="border-slate-200" />

        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-1">Step 2</h3>
            <h4 class="text-lg font-semibold text-slate-900">Schedule & Fare</h4>
            <p class="text-sm text-slate-500 mt-2">When are you leaving and what's the cost per seat?</p>
          </div>

          <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="space-y-2 sm:col-span-2">
                <Label class="text-slate-700">Departure Date & Time</Label>
                <Input type="datetime-local" v-model="form.departure_at" />
                <p v-if="form.errors.departure_at" class="text-xs text-destructive">{{ form.errors.departure_at }}</p>
              </div>
             <div class="space-y-2">
               <Label class="text-slate-700">Available Seats</Label>
               <Input type="number" v-model="form.available_seats" min="1" max="6" />
               <p v-if="form.errors.available_seats" class="text-xs text-destructive">{{ form.errors.available_seats }}</p>
             </div>
             <div class="space-y-2">
               <Label class="text-slate-700">Price (RM)</Label>
               <Input type="number" step="0.10" v-model="form.price" placeholder="0.00" />
               <p v-if="form.errors.price" class="text-xs text-destructive">{{ form.errors.price }}</p>
             </div>
           </div>
         </section>

        <hr class="border-slate-200" />

        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-1">Step 3</h3>
            <h4 class="text-lg font-semibold text-slate-900">Preferences</h4>
            <p class="text-sm text-slate-500 mt-2">Your car details will be attached automatically.</p>
          </div>

          <div class="md:col-span-2 space-y-6">
            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-lg border border-slate-200 border-dashed">
              <Car class="h-6 w-6 text-slate-400" />
              <div class="flex-1">
                <p class="text-xs font-bold text-slate-500 uppercase">Current Vehicle</p>
                <p class="text-sm font-medium text-slate-900">{{ form.car_model }} • {{ form.plate_number }}</p>
              </div>
              <Link href="/car" class="text-xs text-blue-600 font-bold hover:underline italic">Update Car</Link>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="space-y-2">
                <Label class="text-slate-700">Car Model</Label>
                <Input v-model="form.car_model" placeholder="e.g. Perodua Myvi" />
                <p v-if="form.errors.car_model" class="text-xs text-destructive">{{ form.errors.car_model }}</p>
              </div>
              <div class="space-y-2">
                <Label class="text-slate-700">Plate Number</Label>
                <Input v-model="form.plate_number" placeholder="e.g. JQH 1234" class="uppercase font-mono tracking-wider" />
                <p v-if="form.errors.plate_number" class="text-xs text-destructive">{{ form.errors.plate_number }}</p>
              </div>
            </div>
 
            <div class="space-y-2">
              <Label class="text-slate-700">Passenger Preference</Label>
              <Select v-model="form.gender_pref">
                <SelectTrigger>
                  <SelectValue placeholder="Select" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Mixed">Mixed (Default)</SelectItem>
                  <SelectItem value="male">Male Only</SelectItem>
                  <SelectItem value="female">Female Only</SelectItem>
                </SelectContent>
              </Select>
              <p v-if="form.errors.gender_pref" class="text-xs text-destructive">{{ form.errors.gender_pref }}</p>
            </div>

            <div class="space-y-2">
              <Label class="text-slate-700">Driver's Note (Instructions)</Label>
              <Textarea 
                v-model="form.description" 
                placeholder="Where should they wait for you?" 
                class="resize-none h-24"
              />
            </div>
          </div>
        </section>

        <div class="flex items-center justify-end gap-4 pt-8 border-t border-slate-200">
          <Button variant="ghost" asChild class="px-8">
            <Link href="/destination">Discard</Link>
          </Button>
          <Button @click="submit" :disabled="form.processing" class="bg-slate-900 px-12 h-11 text-base">
            {{ form.processing ? 'Posting...' : 'Post Trip Now' }}
          </Button>
        </div>

      </form>
    </div>
  </appLayout>
</template>

<style scoped>
#map-container { overflow: hidden; }
</style>
