<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import uitmUrl from '@/Asset/uitm.png';
import { Card,
  CardAction,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle, } from '@/components/ui/card';

const form = useForm({
    studentID: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'), // Clear password field on failure
    });
};

</script>


<template>
<Head title="Login | E-Tumpang" />
  <div class="flex h-screen items-center justify-center">
    <div class="flex w-full max-w-sm flex-col items-center gap-6">
      <img :src="uitmUrl" alt="UITM Logo" class="h-32 w-32" />
      <Card class="w-full">
        <CardHeader>
          <CardTitle>Login to E-Tumpang</CardTitle>
          <CardDescription>
            Enter your student ID below to login to your account
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit">
            <div class="grid w-full items-center gap-4">
              <div class="flex flex-col space-y-1.5">
                <Label for="studentID">Student ID</Label>
                <Input 
                  id="studentID" 
                  type="text" 
                  v-model="form.studentID" 
                  placeholder="Enter your student ID" 
                  :class="{ 'border-destructive': form.errors.studentID }"
                />
                <span v-if="form.errors.studentID" class="text-xs text-destructive">
                  {{ form.errors.studentID }}
                </span>
              </div>
              <div class="flex flex-col space-y-1.5">
                <Label for="password">Password</Label>
                <Input 
                  id="password" 
                  type="password" 
                  v-model="form.password" 
                  placeholder="password" 
                />
              </div>
            </div>
            
            <button type="submit" class="hidden"></button>
          </form>
        </CardContent>
        <CardFooter class="flex flex-col gap-2">
          <Button 
            class="w-full" 
            :disabled="form.processing" 
            @click="submit"
          >
            {{ form.processing ? 'Logging in...' : 'Login' }}
          </Button>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
