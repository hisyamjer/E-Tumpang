<script setup>
import appLayout from '@/Layouts/sidebar.vue'
import { computed, ref, watch} from 'vue'
import { Head, router, Link, usePage,} from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
import { MapPin, Clock, Calendar, Users, Info, User, Phone, Car, Hash, Search } from 'lucide-vue-next'
import { formatDateDMY, formatTime12h } from '@/lib/datetime'

// Import your existing shadcn dialog components
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
  DialogFooter,
} from '@/components/ui/dialog'

const props = defineProps({
  trips: {
    type: Array,
    default: () => [],
  },
  filters: Object,
})

const search = ref(props.filters?.search || '')

let searchTimeoutId = null;

function ApplySearch() {
  // Clear timeout lama kalau user tengah laju menaip
  if (searchTimeoutId !== null) clearTimeout(searchTimeoutId);
  
  // Set delay 250ms
  searchTimeoutId = setTimeout(() => {
    // Tukar dari { q: filters.q } kepada { search: search.value }
    router.get("/dbpassenger", { search: search.value }, { 
        preserveScroll: true, 
        preserveState: true, 
        replace: true 
    });
  }, 250);
}

const tripRows = computed(() => (Array.isArray(props.trips) ? props.trips : []))

const genderPrefLabel = (pref) => {
  if (!pref) return 'Mixed (Everyone)'
  if (pref === 'Male Only') return 'Lelaki Sahaja (Male Only)'
  if (pref === 'Female Only') return 'Perempuan Sahaja (Female Only)'
  if (pref === 'Mixed') return 'Mixed (Everyone)'
  return pref
}

const seatsLeft = (trip) => {
  const available = Number(trip?.available_seats ?? 0)
  const booked = Number(trip?.bookings_count ?? 0)
  return Math.max(available - booked, 0)
}

const joinTrip = (tripID) => {
  router.post(`/booking/${tripID}`, {}, { preserveScroll: true })
}

const page = usePage()
const message = computed(() => page.props.flash.message)
const error   = computed(() => page.props.flash.error)
</script>

<template>
  <appLayout>
    <Head title="Dashboard | E-Tumpang" />

    <template #header>
      <h1>Dashboard</h1>
    </template>

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-slate-900">Passenger Dashboard</h1>
          <p class="text-muted-foreground text-sm">Browse available trips and book a seat.</p>
        </div>
        <Button variant="outline" as-child>
          <Link href="/booking">My Bookings</Link>
        </Button>
      </div>

      <div v-if="message" class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 font-medium">
        {{ message }}
      </div>
      <div v-if="error" class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800 font-medium">
        {{ error }}
      </div>

      <Separator />

      <div class="flex items-center space-x-2 w-full max-w-sm mb-2">
        <div class="relative w-full">
          <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
          <input 
            v-model="search" 
            @input="ApplySearch"
            type="text" 
            placeholder="Search destination" 
            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 pl-9"
          />
        </div>
      </div>
      
      <div v-if="search" class="text-sm text-muted-foreground mb-4">
        Your search: <span class="font-medium text-primary">{{ search }}</span>
      </div>

      <div v-if="!tripRows.length" class="rounded-lg border bg-muted/30 p-8 text-center">
        <p class="text-sm text-muted-foreground">No available trips right now.</p>
      </div>

      <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card v-for="trip in tripRows" :key="trip.tripID" class="overflow-hidden border-slate-200 shadow-sm transition-all hover:shadow-md">
          <CardHeader class="bg-primary/5 pb-4 space-y-3">
            <div class="flex items-center justify-between gap-2">
                <Badge variant="outline" class="bg-background font-semibold">
                  <Calendar class="mr-1 h-3.5 w-3.5 text-primary" />
                  {{ formatDateDMY(trip.departure_at) }}
                </Badge>
                <Badge variant="outline" class="bg-background font-semibold">
                  <Clock class="mr-1 h-3.5 w-3.5 text-primary" />
                  {{ formatTime12h(trip.departure_at) }}
                </Badge>
              </div>

            <CardTitle class="pt-2 text-base font-bold flex items-start gap-2">
              <MapPin class="mt-0.5 h-4 w-4 text-primary shrink-0" />
              <span>{{ trip.destination }}</span>
            </CardTitle>

            <div class="flex items-center justify-between border-t border-primary/10 pt-3 mt-2">
              <div class="flex items-center gap-2">
                <div class="h-6 w-6 rounded-full bg-primary/10 flex items-center justify-center">
                    <User class="h-3.5 w-3.5 text-primary" />
                </div>
                <span class="text-xs font-bold text-slate-700 truncate max-w-[100px]">{{ trip.student?.name }}</span>
              </div>

              <Dialog>
                <DialogTrigger as-child>
                  <Button variant="ghost" size="sm" class="h-auto p-0 text-[11px] font-bold text-primary hover:bg-transparent hover:underline">
                    <Info class="mr-1 h-3 w-3" /> Driver Details
                  </Button>
                </DialogTrigger>
                
                <DialogContent class="sm:max-w-[350px] rounded-xl">
                  <DialogHeader class="items-center text-center">
                    <div class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center mb-2">
                      <User class="h-8 w-8 text-slate-400" />
                    </div>
                    <DialogTitle class="text-lg font-bold">{{ trip.student?.name }}</DialogTitle>
                    <DialogDescription class="text-xs uppercase tracking-tight font-medium">
                      Verified Driver Profile
                    </DialogDescription>
                  </DialogHeader>

                  <div class="grid gap-3 py-4">
                    <div class="flex items-center justify-between rounded-lg border p-3 bg-slate-50/50">
                      <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                        <Car class="h-4 w-4" /> <span>Vehicle</span>
                      </div>
                      <span class="text-sm font-bold">{{ trip.car_model }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border p-3 bg-slate-50/50">
                      <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                        <Hash class="h-4 w-4" /> <span>Plate No</span>
                      </div>
                      <span class="text-sm font-bold uppercase">{{ trip.plate_number }}</span>
                    </div>
                  </div>

                  <DialogFooter>
                    <Button class="w-full font-bold">
                      <Phone class="mr-2 h-4 w-4" /> {{ trip.student?.phone_number }}
                    </Button>
                  </DialogFooter>
                </DialogContent>
              </Dialog>
            </div>
          </CardHeader>

          <CardContent class="pt-4 space-y-4">
            <div class="flex items-center justify-between text-sm">
              <span class="font-medium flex items-center gap-1.5 text-slate-500">
                <Users class="h-4 w-4" /> Seats available
              </span>
              <Badge :variant="seatsLeft(trip) > 0 ? 'default' : 'secondary'" class="font-bold">
                {{ seatsLeft(trip) }} / {{ trip.available_seats }}
              </Badge>
            </div>

            <div class="flex items-center justify-between gap-3 text-sm">
              <span class="font-medium text-slate-500">Preference:</span>
              <Badge variant="secondary" class="font-bold">
                {{ genderPrefLabel(trip.gender_pref) }}
              </Badge>
            </div>

            <div class="flex items-center justify-between gap-3 text-sm">
              <span class="font-medium text-slate-500">Fare:</span>
              <span class="text-lg font-bold text-primary">RM {{ trip.price }}</span>
            </div>

            <div v-if="trip.description" class="p-3 bg-amber-50/50 border border-amber-100 rounded-lg text-[11px] text-amber-800 flex gap-2 italic">
              <Info class="h-3.5 w-3.5 shrink-0 text-amber-500" />
              <span>"{{ trip.description }}"</span>
            </div>

            <Button
              class="w-full font-bold shadow-sm transition-all active:scale-[0.98]"
              :disabled="seatsLeft(trip) <= 0"
              @click="joinTrip(trip.tripID)"
              :variant="seatsLeft(trip) > 0 ? 'default' : 'secondary'"
            >
              {{ seatsLeft(trip) > 0 ? 'Book My Seat' : 'Trip Full' }}
            </Button>
          </CardContent>
        </Card>
      </div>
    </div>
  </appLayout>
</template>
