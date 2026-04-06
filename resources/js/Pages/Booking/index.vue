<script setup>
import appLayout from '@/Layouts/sidebar.vue'
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
// Added Car and Info icons
import { Calendar, Clock, MapPin, Users, Car, Info, XCircle } from 'lucide-vue-next'

const props = defineProps({
  bookings: {
    type: Array,
    default: () => [],
  },
})

const bookingRows = computed(() => (Array.isArray(props.bookings) ? props.bookings : []))

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

const cancelBooking = (bookingID) => {
    if (confirm('Are you sure you want to cancel this booking?')) {
        router.delete(`/booking/${bookingID}`, {
            preserveScroll: true,
            onSuccess: () => {
                // You can add a toast notification here
            }
        });
    }
}
</script>

<template>
  <appLayout>
    <Head title="My Bookings | E-Tumpang" />

    <template #header>
      <h2>My Bookings</h2>
    </template>

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-slate-900">Passenger Console</h1>
          <p class="text-muted-foreground">Review and manage the trips you’ve joined.</p>
        </div>
        <Button variant="outline" as-child>
          <Link href="/dashboard">Back to Dashboard</Link>
        </Button>
      </div>

      <Separator />

      <div v-if="!bookingRows.length" class="rounded-lg border bg-muted/30 p-12 text-center">
        <div class="bg-white p-3 rounded-full w-fit mx-auto mb-4 shadow-sm border">
          <Car class="h-6 w-6 text-slate-300" />
        </div>
        <h3 class="text-lg font-medium text-slate-900">No bookings yet</h3>
        <p class="text-sm text-slate-500">Go to the dashboard to find an available ride.</p>
      </div>

      <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card
          v-for="booking in bookingRows"
          :key="booking.bookingID"
          class="overflow-hidden border-slate-200"
        >
          <CardHeader class="bg-primary/5 pb-4 space-y-3">
            <div class="flex items-center justify-between gap-2">
              <Badge variant="outline" class="bg-background whitespace-nowrap flex items-center gap-1">
                <Clock class="h-3.5 w-3.5" />
                {{ formatTime(booking.trip?.departure_time) }}
              </Badge>
              <Badge variant="outline" class="bg-background whitespace-nowrap flex items-center gap-1">
                <Calendar class="h-3.5 w-3.5" />
                {{ formatDate(booking.trip?.date) }}
              </Badge>
            </div>

            <CardTitle class="pt-2 text-base font-bold leading-tight flex items-start gap-2">
              <MapPin class="mt-0.5 h-4 w-4 text-primary shrink-0" />
              <span>{{ booking.trip?.destination ?? 'Trip details unavailable' }}</span>
            </CardTitle>

            <div v-if="booking.trip" class="flex flex-col gap-1 border-t border-primary/10 pt-2 mt-1">
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700">
                    <Car class="h-3.5 w-3.5 text-primary" />
                    <span>{{ booking.trip.car_model }} • {{ booking.trip.plate_number }}</span>
                </div>
            </div>
          </CardHeader>

          <CardContent class="pt-4 space-y-4">
            <div v-if="booking.trip?.description" class="p-3 bg-amber-50 border border-amber-100 rounded text-[11px] text-amber-800 flex gap-2 italic">
                <Info class="h-3.5 w-3.5 shrink-0 text-amber-500" />
                <span>"{{ booking.trip.description }}"</span>
            </div>

            <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
              <span class="text-sm font-medium flex items-center gap-1 text-slate-600">
                <Users class="h-4 w-4" />
                Occupancy
              </span>
              <span class="text-sm font-semibold">
                {{ booking.trip?.bookings_count ?? 0 }} / {{ booking.trip?.available_seats ?? 0 }} Taken
              </span>
            </div>

            <div class="flex items-center justify-between gap-3 text-sm">
              <span class="font-medium text-slate-600">Your Preference:</span>
              <Badge variant="secondary" class="text-[10px]">
                {{ genderPrefLabel(booking.trip?.gender_pref) }}
              </Badge>
            </div>

            <div class="pt-2">
                <div class="w-full py-2 bg-green-50 border border-green-100 rounded text-center text-xs font-bold text-green-700 uppercase tracking-tight">
                    Confirmed Booking
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 mt-4">
              <Button 
                  variant="destructive" 
                  class="w-full h-10 gap-2 shadow-sm active:scale-95 transition-transform"
                  @click="cancelBooking(booking.bookingID)"
              >
                  <XCircle class="h-4 w-4" />
                  Cancel My Seat
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </appLayout>
</template>