<script setup>
import appLayout from '@/Layouts/sidebar.vue';
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';
import { Separator } from '@/components/ui/separator';
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';

const props = defineProps({
  trips: {
    type: Array,
    default: () => [],
  },
  trip: {
    type: Array,
    default: () => [],
  },
});

const tripRows = computed(() =>
  Array.isArray(props.trip) && props.trip.length ? props.trip : props.trips
);

const formatTime = (timeString) => {
  if (!timeString) return '';

  // 1. Split "12:00:00" into ["12", "00"]
  const parts = timeString.split(':');
  let hours = parseInt(parts[0]);
  const minutes = parts[1];

  // 2. Determine AM or PM
  const ampm = hours >= 12 ? 'PM' : 'AM';

  // 3. Convert 24h to 12h format
  hours = hours % 12;
  hours = hours ? hours : 12; // Convert "0" to "12"

  // 4. Return formatted string: "12:00 PM"
  return `${hours}:${minutes} ${ampm}`;
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-GB'); 
};

 function cancelTrip(id) {
  if (confirm('Are you sure you want to cancel this trip?')) {
    router.post(`/destination/${id}/delete`)
  }
};

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
      <Button as-child>
        <Link href="/destination/create">
          <Plus class="mr-2 h-4 w-4" /> New Trip
        </Link>
      </Button>
    </div>

    <Separator />

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
      <Card v-for="trip in tripRows" :key="trip.tripID" class="overflow-hidden">
        <CardHeader class="bg-primary/5 pb-4 space-y-3">
          <div class="flex justify-between items-center">
            <Badge variant="outline" class="bg-background whitespace-nowrap">{{ formatTime(trip.departure_time) }}</Badge>
            <Badge variant="outline" class="bg-background whitespace-nowrap">{{ formatDate(trip.date) }}</Badge>
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
          <Progress :value="(trip.bookings_count / trip.available_seats) * 100" class="h-2" />
        </CardContent>

        <CardFooter class="border-t bg-muted/50 gap-2 p-4">
          <Button variant="outline" size="sm" class="flex-1">Edit</Button>
          <Button variant="destructive" size="sm" class="flex-1" @click="cancelTrip(trip.tripID)">Cancel</Button>
        </CardFooter>
      </Card>
    </div>
  </div>
</appLayout>
</template>
