<script setup>
import appLayout from '@/Layouts/sidebar.vue';
import { Head, useForm} from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
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
            <h2>Vehicle Management</h2>
        </template>

        <div class="py-12 px-4 max-w-3xl mx-auto">
            <Card class="border-slate-200 shadow-sm">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Car Details</CardTitle>
                            <CardDescription>Update the vehicle info passengers will see.</CardDescription>
                        </div>
                        <Car class="h-8 w-8 text-slate-400" />
                    </div>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="model">Car Model</Label>
                            <Input 
                                id="model" 
                                v-model="form.model" 
                                :disabled="!isEditing"
                                placeholder="e.g. Perodua Myvi"
                                :class="{'bg-slate-50 border-dashed': !isEditing}"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="plate">Plate Number</Label>
                            <Input 
                                id="plate" 
                                v-model="form.plate_number" 
                                :disabled="!isEditing"
                                placeholder="e.g. JQH 1234"
                                class="uppercase"
                                :class="{'bg-slate-50 border-dashed': !isEditing}"
                            />
                        </div>
                    </form>
                </CardContent>

                <CardFooter class="flex justify-between border-t p-6 bg-slate-50/50 mt-4">
                    <div v-if="!isEditing">
                        <Button type="button" variant="outline" @click="isEditing = true">
                            <Pencil class="mr-2 h-4 w-4" /> Edit Car Details
                        </Button>
                    </div>
                    
                    <div v-else class="flex gap-2">
                        <Button variant="ghost" @click="isEditing = false">
                            <X class="mr-2 h-4 w-4" /> Cancel
                        </Button>
                        <Button @click="submit" :disabled="form.processing">
                            <Save class="mr-2 h-4 w-4" /> {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </Button>
                    </div>
                </CardFooter>
            </Card>
        </div>
    </appLayout>
</template>