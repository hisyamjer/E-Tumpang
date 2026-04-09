<script setup lang="ts">
import appLayout from '@/Layouts/sidebar.vue';
import { Head } from '@inertiajs/vue3';
import { 
    Table, TableBody, TableCell, TableHead, TableHeader, TableRow 
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Users, Calendar, Banknote } from 'lucide-vue-next';

const props = defineProps<{
  history: {
    data : Array<{
      id : number;
      destination : string;
      departure_time : string;
      price : number;
      bookings_count : number;
      status : string;
      date : Date;
    }>;
  };
}>();

const totalEarnings = props.history.data.reduce((acc, trip) => {
  return acc + trip.price * trip.bookings_count;
}, 0);

const totalPassengers = props.history.data.reduce((acc, trip) => {
  return acc + trip.bookings_count;
}, 0);

  
</script>

<template>
    <Head title="History | E-Tumpang" />
    <appLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Trip History</h2>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Collected</CardTitle>
                        <Banknote class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">RM {{ totalEarnings.toFixed(2) }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Passengers Assisted</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ totalPassengers }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Trips Completed</CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ history.data.length }}</div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Past Journeys</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Date & Time</TableHead>
                                <TableHead>Destination</TableHead>
                                <TableHead>Passengers</TableHead>
                                <TableHead>Earning</TableHead>
                                <TableHead class="text-right">Status</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="trip in history.data" :key="trip.id">
                                <TableCell class="font-medium">
                                    {{ trip.date }} <br/>
                                    <span class="text-xs text-muted-foreground">{{ trip.departure_time }}</span>
                                </TableCell>
                                <TableCell>{{ trip.destination }}</TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-1">
                                        <Users class="h-3 w-3" /> {{ trip.bookings_count }}
                                    </div>
                                </TableCell>
                                <TableCell class="font-semibold text-green-700">
                                    RM {{ (trip.price * trip.bookings_count).toFixed(2) }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <Badge variant="secondary" class="bg-green-100 text-green-800">
                                        {{ trip.status }}
                                    </Badge>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="history.data.length === 0">
                                <TableCell colspan="5" class="text-center py-10 text-muted-foreground">
                                    No completed trips found.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </appLayout>
</template>

