<script setup>
import appLayout from '@/Layouts/sidebar.vue';
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus, Users } from 'lucide-vue-next'; 
import { Separator } from '@/components/ui/separator';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { formatDateDMY, formatTime12h } from '@/lib/datetime';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'; 

const props = defineProps({
  trips: {
    type: Array,
    default: () => [],
  },
  hasActiveTrip: {
    type: Boolean,
    default: false
  },
  trip: {
    type: Array,
    default: () => [],
  },
});

const tripRows = computed(() =>
  Array.isArray(props.trip) && props.trip.length ? props.trip : props.trips
);

const genderPrefLabel = (pref) => {
  if (!pref) return 'Mixed (Everyone)';
  if (pref === 'Male Only') return 'Lelaki Sahaja (Male Only)';
  if (pref === 'Female Only') return 'Perempuan Sahaja (Female Only)';
  if (pref === 'Mixed') return 'Mixed (Everyone)';
  return pref;
};

function cancelTrip(id) {
  if (confirm('Are you sure you want to cancel this trip?')) {
    router.post(`/destination/${id}/delete`)
  }
};

const markArrived = (id) => {
  router.post(`/destination/${id}/arrive`)
}

const page = usePage()
const message = computed(() => page.props.flash.message)
const error   = computed(() => page.props.flash.error)

</script>

<template>
<Head title="Destination | E-Tumpang" />
  <appLayout>
    <template #header>
      <h1>Destination</h1>
    </template>
    
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight">Driver Console</h1>
          <p class="text-muted-foreground">Manage your active trips and passengers.</p>
        </div>
        <Button v-if="!hasActiveTrip" as-child>
          <Link href="/destination/create">
            <Plus class="mr-2 h-4 w-4" /> New Trip
          </Link>
        </Button>
      </div>

      <div v-if="message" class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
        {{ message }}
      </div>

      <div v-if="error" class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
        {{ error }}
      </div>

      <Separator />

      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card v-for="trip in tripRows" :key="trip.tripID" class="overflow-hidden">
          <CardHeader class="bg-primary/5 pb-4 space-y-3">
            <div class="flex justify-between items-center">
              <Badge variant="outline" class="bg-background whitespace-nowrap">{{ formatTime12h(trip.departure_at) }}</Badge>
              <Badge variant="outline" class="bg-background whitespace-nowrap">{{ formatDateDMY(trip.departure_at) }}</Badge>
              <Badge :variant="trip.status === 'available' ? 'default' : 'secondary'">
                {{ trip.status }}
              </Badge>
            </div>
            <CardTitle class="pt-2 text-base font-bold leading-tight break-words ">{{ trip.destination }}</CardTitle>
          </CardHeader>
          
          <CardContent class="pt-4">
            <div class="flex items-center justify-between mb-4">
              <span class="text-sm font-medium">Seats Taken:</span>
              <span class="text-sm">{{ trip.bookings_count }} / {{ trip.available_seats }}</span>
            </div>

            <Dialog v-if="trip.bookings_count > 0">
              <DialogTrigger as-child>
                <Button variant="outline" size="sm" class="w-full mb-4 flex gap-2 border-primary/20 hover:bg-primary/5">
                  <Users class="w-4 h-4 text-primary" />
                  View Passengers
                </Button>
              </DialogTrigger>
              <DialogContent class="sm:max-w-[400px]">
                <DialogHeader>
                  <DialogTitle>Passenger List</DialogTitle>
                  <p class="text-sm text-muted-foreground">Students for trip to {{ trip.destination }}</p>
                </DialogHeader>
                <div class="mt-4 divide-y border rounded-xl overflow-hidden bg-card">
                  <div 
                    v-for="booking in trip.bookings" 
                    :key="booking.bookingID" 
                    class="flex items-center gap-4 p-4 hover:bg-muted/30 transition-colors"
                  >
                    <Avatar class="h-10 w-10 border shadow-sm">
                      <AvatarFallback class="bg-primary text-primary-foreground font-bold">
                        {{ booking.student?.name?.[0].toUpperCase() || 'S' }}
                      </AvatarFallback>
                    </Avatar>
                    <div class="flex flex-col min-w-0">
                      <p class="text-sm font-bold leading-none truncate">{{ booking.student?.name }}</p>
                      <p class="text-[11px] text-muted-foreground uppercase tracking-widest mt-1.5 font-medium">
                        Phone: {{ booking.student?.phone_number }}
                      </p>
                    </div>
                  </div>
                </div>
              </DialogContent>
            </Dialog>

            <div v-else class="text-center py-2 mb-4 border border-dashed rounded-md bg-muted/10">
              <p class="text-[10px] text-muted-foreground uppercase tracking-wider font-bold">Waiting for Bookings</p>
            </div>
            <div class="mt-4 flex items-center justify-between gap-3">
              <span class="text-sm font-medium">Passenger Preference:</span>
              <Badge variant="secondary" class="whitespace-nowrap">
                {{ genderPrefLabel(trip.gender_pref) }}
              </Badge>
            </div>
          </CardContent>

          <CardFooter class="border-t bg-muted/50 gap-2 p-4">
            <Button variant="destructive" size="sm" class="flex-1 " @click="cancelTrip(trip.tripID)">Cancel</Button>
            <Button v-if="trip.status === 'available'" class="bg-green-600 hover:bg-green-700 text-white" @click="markArrived(trip.tripID)">
                Mark as Arrived
            </Button>

            <Badge v-if="trip.status === 'completed'" variant="secondary">
                Completed
            </Badge>
          </CardFooter>
        </Card>
      </div>
    </div>
  </appLayout>
</template>
