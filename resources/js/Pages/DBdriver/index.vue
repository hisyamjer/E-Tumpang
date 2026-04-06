<script setup>
import appLayout from '@/Layouts/sidebar.vue';
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { Plus, Car, MapPin, Users } from 'lucide-vue-next';

const props = defineProps({
  trips: {
    type: Array,
    default: () => [],
  },
});

const tripRows = computed(() => (Array.isArray(props.trips) ? props.trips : []));

const formatTime = (timeString) => {
  if (!timeString) return '';
  const parts = String(timeString).split(':');
  let hours = parseInt(parts[0], 10);
  const minutes = parts[1] ?? '00';
  const ampm = hours >= 12 ? 'PM' : 'AM';
  hours = hours % 12;
  hours = hours ? hours : 12;
  return `${hours}:${minutes} ${ampm}`;
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-GB');
};
</script>

<template>
<Head title="Dashboard | E-Tumpang" />
  <appLayout>
    <template #header>
      <h1>Dashboard</h1>
    </template>

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight">Driver Dashboard</h1>
          <p class="text-muted-foreground">Quick access to your trips and vehicle.</p>
        </div>
        <Button as-child>
          <Link href="/destination/create">
            <Plus class="mr-2 h-4 w-4" /> Post Trip
          </Link>
        </Button>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium flex items-center gap-2">
              <MapPin class="h-4 w-4" /> Trips
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ tripRows.length }}</div>
            <p class="text-xs text-muted-foreground">Your posted trips</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium flex items-center gap-2">
              <Users class="h-4 w-4" /> Seats Taken
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">
              {{ tripRows.reduce((sum, t) => sum + (t.bookings_count ?? 0), 0) }}
            </div>
            <p class="text-xs text-muted-foreground">Across recent trips</p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium flex items-center gap-2">
              <Car class="h-4 w-4" /> Vehicle
            </CardTitle>
          </CardHeader>
          <CardContent>
            <Button variant="outline" as-child class="w-full">
              <Link href="/car">Manage vehicle</Link>
            </Button>
          </CardContent>
        </Card>
      </div>

      <Separator />

      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Recent Trips</h2>
        <Button variant="outline" as-child>
          <Link href="/destination">View all</Link>
        </Button>
      </div>

      <div v-if="!tripRows.length" class="rounded-lg border bg-muted/30 p-8 text-center">
        <p class="text-sm text-muted-foreground">No trips yet. Post your first trip.</p>
      </div>

      <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card v-for="trip in tripRows" :key="trip.tripID" class="overflow-hidden">
          <CardHeader class="bg-primary/5 pb-4 space-y-3">
            <div class="flex items-center justify-between gap-2">
              <Badge variant="outline" class="bg-background whitespace-nowrap">
                {{ formatDate(trip.date) }}
              </Badge>
              <Badge variant="outline" class="bg-background whitespace-nowrap">
                {{ formatTime(trip.departure_time) }}
              </Badge>
              <Badge :variant="trip.status === 'available' ? 'default' : 'secondary'">
                {{ trip.status }}
              </Badge>
            </div>
            <CardTitle class="pt-2 text-base font-bold leading-tight break-words">
              {{ trip.destination }}
            </CardTitle>
          </CardHeader>

          <CardContent class="pt-4 space-y-2">
            <div class="flex items-center justify-between text-sm">
              <span class="font-medium">Seats:</span>
              <span>{{ trip.bookings_count }} / {{ trip.available_seats }}</span>
            </div>
            <Button variant="outline" as-child class="w-full">
              <Link href="/destination">Manage in Destination</Link>
            </Button>
          </CardContent>
        </Card>
      </div>
    </div>
  </appLayout>
</template>
