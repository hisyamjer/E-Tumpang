<script setup>
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card,
  CardAction,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle, } from '@/components/ui/card';
</script>





<template>
  <Card class="w-full max-w-sm">
    <CardHeader>
      <CardTitle>Login to E-Tumpang</CardTitle>
      <CardDescription>
        Enter your studentID below to login to your account
      </CardDescription>
    </CardHeader>
    <CardContent>
      <form>
        <div class="grid w-full items-center gap-4">
          <div class="flex flex-col space-y-1.5">
            <Label for="email">Student ID</Label>
            <Input id="email" type="text" placeholder="Enter your student ID" />
          </div>
          <div class="flex flex-col space-y-1.5">
            <div class="flex items-center">
              <Label for="password">Password</Label>
            </div>
            <Input id="password" type="password" />
          </div>
        </div>
      </form>
    </CardContent>
    <CardFooter class="flex flex-col gap-2">
      <Button class="w-full">
        Login
      </Button>
    </CardFooter>
  </Card>
</template>