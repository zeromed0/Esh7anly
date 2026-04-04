
<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 flex flex-col">

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-blue-600 text-white shadow-md flex items-center justify-between px-3 py-2">

      <!-- Left -->
      <div class="flex items-center space-x-2">
        <button @click="menuOpen = !menuOpen"
                class="p-2 rounded-md hover:bg-blue-700 transition">
          <Menu class="w-6 h-6 text-white" />
        </button>

        <div class="flex items-center cursor-pointer"
             @click="go('/user/dashboard')">
          <img src="/images/logo1.png" class="h-10 w-auto" />
        </div>
      </div>

      <!-- Right -->
      <div class="flex items-center space-x-2">

        <!-- 🔔 Notifications -->
        <div class="relative">

          <button @click="toggleNotifications"
                  class="relative p-2 rounded-full hover:bg-blue-700 transition">
            <Bell class="w-5 h-5 text-white" />

            <span v-if="notifications.length"
                  class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">
              {{ notifications.length }}
            </span>
          </button>

          <!-- Notifications Box (أصغر وأنسب للموبايل) -->
          <div v-if="showNotifications"
               class="absolute right-0 mt-2 w-64 bg-white text-gray-700 rounded-xl shadow-xl overflow-hidden z-50">

            <div class="px-3 py-2 border-b font-semibold text-blue-600 text-sm">
              Notifications
            </div>

            <div v-if="notifications.length === 0"
                 class="p-3 text-center text-gray-500 text-xs">
              No notifications
            </div>

            <div v-for="(n,index) in notifications"
                 :key="n.id"
                 class="flex justify-between items-start px-3 py-2 hover:bg-gray-50 border-b">

              <div class="flex-1 cursor-pointer"
                   @click="openTransaction">

                <span class="text-green-600 font-semibold text-[10px]">
                  ✔ Completed
                </span>

                <p class="text-xs font-semibold text-gray-800">
                  Order #{{ n.id }}
                </p>

                <p class="text-[10px] text-gray-400">
                  {{ formatDate(n.created_at) }}
                </p>
              </div>

              <button @click.stop="removeNotification(index)"
                      class="text-gray-400 hover:text-red-500 text-xs ml-2">
                ✕
              </button>
            </div>

            <div class="text-center text-xs py-2 text-blue-600 cursor-pointer hover:bg-gray-50"
                 @click="openTransaction">
              View all
            </div>

          </div>
        </div>

        <!-- Profile -->
        <button @click="go('/user/profile')"
                class="flex items-center justify-center p-2 rounded-full hover:bg-blue-700 transition">
          <User class="w-5 h-5 text-white" />
        </button>

      </div>
    </header>

    <!-- Sidebar (محسن للموبايل) -->
    <transition name="slide">
      <aside v-if="menuOpen"
             class="fixed top-[56px] left-0 h-[calc(100%-56px)] w-56 bg-white shadow-xl z-40 flex flex-col justify-between">

        <div class="px-2">
          <nav class="mt-4 space-y-2 text-gray-700">

            <button v-for="link in links"
                    :key="link.path"
                    @click="go(link.path)"
                    class="flex items-center space-x-3 w-full text-left py-2 px-3 rounded-lg transition text-sm"
                    :class="isActive(link.path)
                      ? 'bg-blue-100 text-blue-700 font-semibold'
                      : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'">

              <component :is="link.icon"
                         class="w-5 h-5 text-blue-500" />

              <span>{{ getLabel(link.label) }}</span>
            </button>

          </nav>
        </div>

        <button @click="logout"
                class="border-t w-full p-3 flex items-center space-x-2 text-sm text-gray-700 hover:bg-gray-100 transition">
          <LogOut class="w-4 h-4 text-red-500" />
          <span>{{ $page.props.auth.user.name }}</span>
        </button>

      </aside>
    </transition>

    <div v-if="menuOpen"
         class="fixed inset-0 bg-black/40 z-30"
         @click="menuOpen = false">
    </div>

    <!-- Main -->
    <main class="flex-1 pt-14 px-3 overflow-y-auto">
      <slot />
    </main>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { router } from "@inertiajs/vue3"
import { Menu, User, LogOut, Home, Bolt, Ticket, Wallet, Receipt, Bell } from "lucide-vue-next"

const menuOpen = ref(false)

// Notifications
const notifications = ref([])
const showNotifications = ref(false)

const toggleNotifications = () => { showNotifications.value = !showNotifications.value }
const removeNotification = (index) => { notifications.value.splice(index,1) }
const openTransaction = () => { showNotifications.value = false; router.visit('/user/transactions') }

const loadNotifications = () => {
  fetch("/user/notifications")
    .then(res => res.json())
    .then(data => { notifications.value = data })
}

onMounted(() => { loadNotifications() })

// Navigation
const go = (path) => { menuOpen.value = false; router.visit(path) }
const logout = () => router.post("/logout")

// Sidebar links
const links = [
  { path: "/user/dashboard", label: "dashboard", icon: Home },
  { path: "/user/topup", label: "topups", icon: Bolt },
  { path: "/user/vouchers", label: "vouchers", icon: Ticket },
  { path: "/user/transactions", label: "transactions", icon: Receipt },
  { path: "/user/wallet", label: "wallet", icon: Wallet },
  { path: "/user/profile", label: "profile", icon: User },
]

const isActive = (path) => window.location.pathname === path

const getLabel = (key) => {
  const labels = {
    dashboard: "Dashboard",
    topups: "Topups",
    vouchers: "Vouchers",
    transactions: "Transactions",
    wallet: "Wallet",
    profile: "Profile",
  }
  return labels[key] || key
}

const formatDate = (date) =>
  new Date(date).toLocaleString("fr-MR", { timeZone: "Africa/Nouakchott" })
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.25s ease;
}
.slide-enter-from,
.slide-leave-to {
  transform: translateX(-100%);
}
</style>