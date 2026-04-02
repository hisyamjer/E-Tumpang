<script setup>
import { Head, router, usePage } from '@inertiajs/vue3'
import { Card, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Car, User } from 'lucide-vue-next'

const page = usePage()
const currentRole = page.props.value?.auth?.role ?? null

const selectRole = (role) => {
  // Tell the backend which mode the user is in
  router.post('/set-role', { role: role })
}
</script>

<template>
  <Head title="Choose Role - E-Tumpang" />

  <div class="flex h-screen items-center justify-center gap-6">
    <Card @click="selectRole('driver')" class="cursor-pointer hover:border-primary transition-all w-64 text-center">
      <CardHeader>
        <div class="flex justify-center pb-4"><Car :size="48" /></div>
        <CardTitle>Driver</CardTitle>
        <CardDescription>I have a vehicle and want to offer rides.</CardDescription>
      </CardHeader>
    </Card>

    <Card @click="selectRole('passenger')" class="cursor-pointer hover:border-primary transition-all w-64 text-center">
      <CardHeader>
        <div class="flex justify-center pb-4"><User :size="48" /></div>
        <CardTitle>Passenger</CardTitle>
        <CardDescription>I am looking for a ride to my destination.</CardDescription>
      </CardHeader>
    </Card>
  </div>
</template>
