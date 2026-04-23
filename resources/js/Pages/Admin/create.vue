<script setup lang="ts">
import appLayout from '@/Layouts/sidebar.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { ChevronLeft } from 'lucide-vue-next'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

const form = useForm({
  studentID: '',
  name: '',
  email: '',
  password: '',
  gender: '',
  phone_number: '',
  role: '',
  plate_number: '',
  model: '',
  is_default: false,
})

const submit = () => {
  form.post('/admin/users', {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <Head title="Admin | Create Student" />

  <appLayout>
    <template #header>
      <div class="flex items-center gap-4">
        <Link href="/admin/users" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
          <ChevronLeft class="h-5 w-5 text-slate-600" />
        </Link>
        <h2 class="">Add Student</h2>
      </div>
    </template>

    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">New Student</h3>
        <Button as-child variant="outline">
          <Link href="/admin/users">Back</Link>
        </Button>
      </div>

      <form class="space-y-6 rounded-lg border bg-background p-6" @submit.prevent="submit">
        <div class="grid gap-4 md:grid-cols-2">
          <div class="space-y-2">
            <Label for="studentID">Student ID</Label>
            <Input id="studentID" v-model="form.studentID" type="text" />
            <p v-if="form.errors.studentID" class="text-xs text-destructive">{{ form.errors.studentID }}</p>
          </div>

          <div class="space-y-2">
            <Label for="name">Name</Label>
            <Input id="name" v-model="form.name" type="text" />
            <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
          </div>

          <div class="space-y-2">
            <Label for="email">Email</Label>
            <Input id="email" v-model="form.email" type="email" />
            <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
          </div>

          <div class="space-y-2">
            <Label for="phone_number">Phone Number</Label>
            <Input id="phone_number" v-model="form.phone_number" type="text" />
            <p v-if="form.errors.phone_number" class="text-xs text-destructive">{{ form.errors.phone_number }}</p>
          </div>

          <div class="space-y-2">
            <Label for="password">Password</Label>
            <Input id="password" v-model="form.password" type="password" />
            <p v-if="form.errors.password" class="text-xs text-destructive">{{ form.errors.password }}</p>
          </div>

          <div class="space-y-2">
            <Label for="role">Role (optional)</Label>
            <Input id="role" v-model="form.role" type="text" placeholder="driver or passenger" />
            <p v-if="form.errors.role" class="text-xs text-destructive">{{ form.errors.role }}</p>
          </div>

          <div class="space-y-2">
            <Label for="plate_number">Plate Number (optional)</Label>
            <Input id="plate_number" v-model="form.plate_number" type="text" />
            <p v-if="form.errors.plate_number" class="text-xs text-destructive">{{ form.errors.plate_number }}</p>
          </div>

          <div class="space-y-2">
            <Label for="model">Car Model (optional)</Label>
            <Input id="model" v-model="form.model" type="text" />
            <p v-if="form.errors.model" class="text-xs text-destructive">{{ form.errors.model }}</p>
          </div>

          <div class="space-y-2">
            <Label for="gender">Gender</Label>
            <Select v-model="form.gender">
              <SelectTrigger id="gender">
                <SelectValue placeholder="Select Gender" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="male">Male</SelectItem>
                <SelectItem value="female">Female</SelectItem>
              </SelectContent>
            </Select>
            <p v-if="form.errors.gender" class="text-xs text-destructive">{{ form.errors.gender }}</p>
          </div>
        </div>

        <div class="flex justify-end gap-2">
          <Button variant="outline" as-child>
            <Link href="/admin/users">Cancel</Link>
          </Button>
          <Button type="submit" :disabled="form.processing">
            {{ form.processing ? 'Creating...' : 'Create' }}
          </Button>
        </div>
      </form>
    </div>
  </appLayout>
</template>
