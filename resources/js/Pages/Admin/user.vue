<script setup lang="ts">
import appLayout from '@/Layouts/sidebar.vue'
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

type StudentRow = {
  studentID: string
  name: string
  email: string
  phone_number: string | null
  is_blocked: boolean
  role: string | null
  plate_number: string | null
  model: string | null
}

const props = defineProps<{
  students: StudentRow[]
}>()

const display = (value: string | null | undefined) => (value && value.trim() ? value : '-')

const page = usePage<{ flash: { message: string } }>()
const message = computed(() => page.props.flash.message)

</script>

<template>
  <Head title="Admin | E-Tumpang" />

  <appLayout>
    <template #header>
      <h2>Students</h2>
    </template>

    <div v-if="message" class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
        {{ message }}
      </div>

    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">All Students</h3>
          <Button as-child>
            <Link href="/admin/users/create">Add New User</Link>
          </Button>
      </div>

      <div class="rounded-lg border bg-background">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Student ID</TableHead>
              <TableHead>Name</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Phone</TableHead>
              <TableHead>Role</TableHead>
              <TableHead>Plate No.</TableHead>
              <TableHead>Model</TableHead>
              <TableHead>Action</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
              <TableRow v-for="student in props.students" :key="student.studentID">
                <TableCell class="font-medium">{{ student.studentID }}</TableCell>
                <TableCell>{{ student.name }}</TableCell>
                <TableCell>{{ student.email }}</TableCell>
                <TableCell>{{ display(student.phone_number) }}</TableCell>
                <TableCell>{{ display(student.role) }}</TableCell>
                <TableCell>{{ display(student.plate_number) }}</TableCell>
                <TableCell>{{ display(student.model) }}</TableCell>
                <TableCell>
                  <Button v-if="!student.is_blocked" as-child variant="destructive" size="sm">
                    <Link :href="`/admin/users/${student.studentID}/block`" method="post" as="button">
                      Block
                    </Link>
                  </Button>
                  <Button v-else as-child variant="outline" size="sm">
                    <Link :href="`/admin/users/${student.studentID}/unblock`" method="post" as="button">
                      Unblock
                    </Link>
                  </Button>
                </TableCell>
              </TableRow>
            <TableRow v-if="props.students.length === 0">
              <TableCell colspan="8" class="text-center py-10 text-muted-foreground">
                No students found.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </appLayout>
</template>
