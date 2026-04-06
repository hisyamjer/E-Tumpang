<script setup>
import appLayout from '@/Layouts/sidebar.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Pencil, Car, Save, X } from 'lucide-vue-next';

const props = defineProps({
    auth: Object
});

const isEditing = ref(false);

const form = useForm({
    model: props.auth.user.model || '',
    plate_number: props.auth.user.plate_number || '',
});

const submit = () => {
    form.post(`/car`, {
        onSuccess: () => isEditing.value = false,
    });
};
</script>

<template>
    <Head title="Car Details | E-Tumpang" />
    <appLayout>
        <template #header>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Vehicle Management</h2>
        </template>

        <div class="py-10 px-6 max-w-2xl mx-auto">
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-200">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Car Details</h3>
                    <p class="text-sm text-slate-500">Update the vehicle info passengers will see when you post a trip.</p>
                </div>
                <div class="bg-slate-100 p-3 rounded-full">
                    <Car class="h-6 w-6 text-slate-600" />
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <div class="grid gap-6">
                    <div class="space-y-2">
                        <Label for="model" class="text-sm font-medium text-slate-700">Car Model</Label>
                        <Input 
                            id="model" 
                            v-model="form.model" 
                            :disabled="!isEditing"
                            placeholder="e.g. Perodua Myvi"
                            class="transition-all duration-200"
                            :class="!isEditing ? 'bg-slate-50 border-transparent shadow-none cursor-not-allowed' : 'bg-white border-slate-300 focus:ring-2 focus:ring-blue-500'"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="plate" class="text-sm font-medium text-slate-700">Plate Number</Label>
                        <Input 
                            id="plate" 
                            v-model="form.plate_number" 
                            :disabled="!isEditing"
                            placeholder="e.g. JQH 1234"
                            class="uppercase transition-all duration-200 font-mono tracking-wider"
                            :class="!isEditing ? 'bg-slate-50 border-transparent shadow-none cursor-not-allowed' : 'bg-white border-slate-300 focus:ring-2 focus:ring-blue-500'"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <div v-if="!isEditing">
                        <Button type="button" variant="outline" @click="isEditing = true" class="px-6">
                            <Pencil class="mr-2 h-4 w-4" /> Edit Details
                        </Button>
                    </div>
                    
                    <div v-else class="flex gap-3">
                        <Button type="button" variant="ghost" @click="isEditing = false" class="text-slate-600">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="form.processing" class="px-8 bg-blue-600 hover:bg-blue-700">
                            <Save class="mr-2 h-4 w-4" /> {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </appLayout>
</template>