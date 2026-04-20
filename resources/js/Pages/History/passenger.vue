<script setup lang="ts">
import { ref } from 'vue';
import appLayout from '@/Layouts/sidebar.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { 
    Dialog, DialogContent, DialogDescription, 
    DialogFooter, DialogHeader, DialogTitle, DialogTrigger 
} from '@/components/ui/dialog';
import { Textarea } from '@/components/ui/textarea';
import { MapPin, Clock, Star, Receipt } from 'lucide-vue-next';

const props = defineProps<{
    history: {
        data: Array<{
            id: number;
            destination: string;
            departure_time: string;
            date: string;
            price: number;
            status: string;
            has_feedback: boolean;
        }>;
    };
}>();

// --- Feedback Logic ---
const isDialogOpen = ref(false);
const selectedTripId = ref<number | null>(null);

const form = useForm({
    tripID: null as number | null,
    rating: 5,
    comment: '',
});

const openFeedbackModal = (tripId: number) => {
    form.tripID = tripId;
    form.rating = 5;
    form.comment = '';
    isDialogOpen.value = true;
};

const submitFeedback = () => {
    form.post('/feedback', {
        onSuccess: () => {
            isDialogOpen.value = false;
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Trip History" />

    <appLayout>
        <div class="py-10 px-6 max-w-3xl mx-auto">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold tracking-tight">Trip History</h2>
                <p class="text-sm text-slate-500">View and rate your past journeys.</p>
            </div>

            <div class="space-y-4">
                <div v-for="trip in history.data" :key="trip.id" 
                    class="group flex items-center justify-between p-4 bg-white border border-slate-100 rounded-xl hover:shadow-sm transition-all"
                >
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-green-50 group-hover:text-green-600 transition-colors">
                            <MapPin class="w-5 h-5" />
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-slate-900">{{ trip.destination }}</h4>
                            <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                                <span>{{ trip.date }}</span>
                                <span class="text-slate-300">•</span>
                                <span class="font-medium text-slate-700">RM {{ trip.price.toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <Button 
                            v-if="!trip.has_feedback && trip.status === 'completed'"
                            @click="openFeedbackModal(trip.id)"
                            variant="ghost"
                            class="text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 font-semibold"
                        >
                            Rate Trip
                        </Button>

                        <div v-else-if="trip.has_feedback" class="flex items-center gap-1.5 text-slate-400 text-xs font-medium px-3 py-1 bg-slate-50 rounded-full">
                            <Star class="w-3 h-3 fill-current" />
                            Rated
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="sm:max-w-[400px] border-none shadow-2xl rounded-3xl">
                <DialogHeader>
                    <DialogTitle class="text-center text-xl font-bold">How was the ride?</DialogTitle>
                </DialogHeader>

                <div class="py-6 flex flex-col items-center space-y-6">
                    <div class="flex gap-2">
                        <button 
                            v-for="star in 5" :key="star" 
                            @click="form.rating = star"
                            type="button"
                            class="hover:scale-110 active:scale-95 transition-all"
                        >
                            <Star 
                                :class="star <= form.rating ? 'text-yellow-400 fill-yellow-400' : 'text-slate-200'" 
                                class="w-10 h-10" 
                            />
                        </button>
                    </div>

                    <Textarea 
                        v-model="form.comment" 
                        placeholder="Add a comment..." 
                        class="bg-slate-50 border-none rounded-2xl focus-visible:ring-1 focus-visible:ring-slate-200 resize-none min-h-[100px]"
                    />
                </div>

                <DialogFooter>
                    <Button 
                        @click="submitFeedback" 
                        :disabled="form.processing"
                        class="w-full bg-slate-900 hover:bg-black py-6 rounded-2xl text-base font-bold transition-all shadow-lg"
                    >
                        {{ form.processing ? 'Sending...' : 'Submit Feedback' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </appLayout>
</template>