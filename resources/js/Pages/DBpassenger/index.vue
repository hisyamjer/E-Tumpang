<script setup>
import appLayout from '@/Layouts/sidebar.vue'
import { computed } from 'vue'
import { Head, router, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
import { MapPin, Clock, Calendar, Users, Info } from 'lucide-vue-next'

const props = defineProps({
  trips: {
    type: Array,
    default: () => [],
  },
})

const tripRows = computed(() => (Array.isArray(props.trips) ? props.trips : []))

const formatTime = (timeString) => {
  if (!timeString) return ''
  const parts = String(timeString).split(':')
  let hours = parseInt(parts[0], 10)
  const minutes = parts[1] ?? '00'
  const ampm = hours >= 12 ? 'PM' : 'AM'
  hours = hours % 12
  hours = hours ? hours : 12
  return `${hours}:${minutes} ${ampm}`
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-GB')
}

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
          <h1 class="text-2xl font-bold tracking-tight">Passenger Dashboard</h1>
          <p class="text-muted-foreground">Browse available trips and book a seat.</p>
        </div>
        <Button variant="outline" as-child>
          <Link href="/booking">My Bookings</Link>
        </Button>
      </div>

      <Separator />

      <div v-if="!tripRows.length" class="rounded-lg border bg-muted/30 p-8 text-center">
        <p class="text-sm text-muted-foreground">No available trips right now.</p>
      </div>

      <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card v-for="trip in tripRows" :key="trip.tripID" class="overflow-hidden">
          <CardHeader class="bg-primary/5 pb-4 space-y-3">
            <div class="flex items-center justify-between gap-2">
              <Badge variant="outline" class="bg-background whitespace-nowrap flex items-center gap-1">
                <Calendar class="h-3.5 w-3.5" />
                {{ formatDate(trip.date) }}
              </Badge>
              <Badge variant="outline" class="bg-background whitespace-nowrap flex items-center gap-1">
                <Clock class="h-3.5 w-3.5" />
                {{ formatTime(trip.departure_time) }}
              </Badge>
            </div>

            <CardTitle class="pt-2 text-base font-bold leading-tight break-words flex items-start gap-2">
              <MapPin class="mt-0.5 h-4 w-4 text-primary shrink-0" />
              <span>{{ trip.destination }}</span>
            </CardTitle>

            <div class="flex flex-col gap-1 border-t border-primary/10 pt-2 mt-2">
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700">
                    <Car class="h-3.5 w-3.5 text-primary" />
                    <span>{{ trip.car_model }} • {{ trip.plate_number }}</span>
                </div>
                <p class="text-[10px] text-muted-foreground uppercase tracking-tight">
                    Driver: {{ trip.student?.name }}
                </p>
            </div>
          </CardHeader>

          <CardContent class="pt-4 space-y-4">
            <div class="flex items-center justify-between text-sm">
              <span class="font-medium flex items-center gap-1">
                <Users class="h-4 w-4" />
                Seats left
              </span>
              <Badge :variant="seatsLeft(trip) > 0 ? 'default' : 'secondary'">
                {{ seatsLeft(trip) }} / {{ trip.available_seats }}
              </Badge>
            </div>

            <div class="flex items-center justify-between gap-3">
              <span class="text-sm font-medium">Passenger Preference:</span>
              <Badge variant="secondary" class="whitespace-nowrap">
                {{ genderPrefLabel(trip.gender_pref) }}
              </Badge>
            </div>

            <div class="flex items-center justify-between gap-3">
              <span class="text-sm font-medium">Price:</span>
              <span class="text-sm font-semibold">RM {{ trip.price }}</span>
            </div>

            <div v-if="trip.description" class="p-2 bg-amber-50 border border-amber-200 rounded text-[11px] text-amber-800 flex gap-2 italic">
                <Info class="h-3.5 w-3.5 shrink-0 text-amber-600" />
                <span>"{{ trip.description }}"</span>
            </div>

            <Button
              class="w-full"
              :disabled="seatsLeft(trip) <= 0"
              @click="joinTrip(trip.tripID)"
            >
              {{ seatsLeft(trip) > 0 ? 'Book Seat' : 'Full' }}
            </Button>
          </CardContent>
        </Card>
      </div>
    </div>
  </appLayout>
</template>
