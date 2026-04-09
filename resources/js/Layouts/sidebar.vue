<script setup>
  import { computed } from 'vue'
  import { Link, usePage } from '@inertiajs/vue3'
  import { Home, Car, Settings, LogOut, ChevronsUpDown, UserCircle, History, User } from 'lucide-vue-next'
  import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarInset,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarProvider,
  SidebarRail,
  SidebarTrigger,
} from '@/components/ui/sidebar'

import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

  import uitmUrl from '@/Asset/uitm.png'
 
  const page = usePage()
 
  // 1. Get user data from our Student model (via Inertia share)
  const user = computed(() => page.props.auth?.user ?? null)
  const role = computed(() => page.props.auth?.role ?? null)
 
  // 2. Formatting values for display
  const userName = computed(() => user.value?.name ?? 'User')
  const studentId = computed(() => user.value?.studentID ?? '')
  const userRole = computed(() => {
    if (role.value === 'driver') return 'Driver'
    if (role.value === 'passenger') return 'Passenger'
    return 'Student'
  })
 
  // 3. Generate initials (e.g., "Muhammad Ali" -> "MA")
  const userInitials = computed(() => {
    const name = userName.value
    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) || 'U'
  })
 
  const menuItems = computed (() => {
    if (role.value === 'driver') {
      return [
        { name: 'Home', icon: Home, link: '/dashboard' },
        { name: 'Destination', icon: Car, link: '/destination' },
        { name: 'My Vehicles', icon: UserCircle, link: '/car'},
        { name: 'History', icon: History, link: '/history' }

      ]
    } else {
      return [
        { name: 'Home', icon: Home, link: '/dashboard' },
        { name: 'Booking', icon: Car, link: '/booking' },
      ]
    }
  })
</script>

<template>
  <SidebarProvider>
    <Sidebar>
      <SidebarHeader>
        <SidebarMenu>
          <SidebarMenuItem>
            <SidebarMenuButton size="lg">
              <img :src="uitmUrl" alt="UITM Logo" class="h-32 w-32" />
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>

      <SidebarContent>
        <SidebarGroup>
          <SidebarGroupLabel>Menu</SidebarGroupLabel>
          <SidebarGroupContent>
            <SidebarMenu>
              <SidebarMenuItem v-for="item in menuItems" :key="item.link">
                <SidebarMenuButton as-child>
                  <Link :href="item.link">
                    <component :is="item.icon" />
                    <span>{{ item.name }}</span>
                  </Link>
                </SidebarMenuButton>
              </SidebarMenuItem>
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>
      </SidebarContent>

      <!-- FOOTER START: The User Profile Section -->
      <SidebarFooter>
        <SidebarMenu>
          <SidebarMenuItem>
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <SidebarMenuButton
                  size="lg"
                  class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                >
                  <div class="flex aspect-square size-8 items-center justify-center rounded-lg bg-muted font-medium text-xs">
                    {{ userInitials }}
                  </div>
                  <div class="grid flex-1 text-left text-sm leading-tight">
                    <span class="truncate font-semibold">{{ userName }}</span>
                    <span class="truncate text-xs text-muted-foreground">{{ userRole }}</span>
                  </div>
                  <ChevronsUpDown class="ml-auto size-4" />
                </SidebarMenuButton>
              </DropdownMenuTrigger>

              <DropdownMenuContent
                side="top"
                align="start"
                class="w-56 rounded-lg"
              >
                <DropdownMenuLabel class="p-0 font-normal">
                  <div class="flex items-center gap-2 px-2 py-1.5 text-left text-sm">
                    <div class="flex aspect-square size-8 items-center justify-center rounded-lg bg-muted font-medium text-xs">
                      {{ userInitials }}
                    </div>
                    <div class="grid flex-1 text-left text-sm leading-tight">
                      <span class="truncate font-semibold">{{ userName }}</span>
                      <span class="truncate text-xs text-muted-foreground">{{ studentId }}</span>
                    </div>
                  </div>
                </DropdownMenuLabel>
                
                <DropdownMenuSeparator />

                <DropdownMenuItem>
                  <User class="size-4 mr-2" />
                  <Link 
                    href="/reset-role" 
                    method="post"
                  >
                    Change Role
                  </Link>
                </DropdownMenuItem>

                <DropdownMenuSeparator />

                <DropdownMenuItem as-child>
                  <Link 
                    href="/logout" 
                    method="post" 
                    as="button" 
                    class="w-full flex items-center text-red-500 hover:text-red-600 cursor-pointer"
                  >
                    <LogOut class="size-4 mr-2" />
                    <span>Log Out</span>
                  </Link>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarFooter>
      <!-- FOOTER END -->

      <SidebarRail />
    </Sidebar>

    <SidebarInset>
      <header class="flex h-16 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12 border-b">
        <div class="flex w-full items-center gap-2 px-4">
          <SidebarTrigger class="-ml-1" />
          <div class="flex-1">
            <slot name="header" />
          </div>
        </div>
      </header>
      <main class="flex flex-1 flex-col gap-4 p-4 pt-0">
        <slot />
      </main>
    </SidebarInset>
  </SidebarProvider>
</template>
