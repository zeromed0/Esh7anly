<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-100 flex flex-col">
    <!-- Header -->
    <header
      class="fixed top-0 left-0 right-0 z-50 bg-blue-600 text-white shadow-md flex items-center justify-between px-4 py-3"
    >
      <!-- Left: Hamburger + Logo -->
      <div class="flex items-center space-x-2">
        <!-- Hamburger -->
        <button
          @click="menuOpen = !menuOpen"
          class="p-2 rounded-md hover:bg-blue-700 transition"
        >
          <Menu class="w-6 h-6 text-white" />
        </button>

        <!-- Logo -->
        <div class="flex items-center space-x-2 cursor-pointer" @click="go('/user/dashboard')">
          <img src="/images/logo.png" alt="Logo" class="h-12 w-auto" />
          <span class="font-bold text-lg sm:text-xl">2Sh7anli</span>
        </div>
      </div>
        

      <!-- Right: Language + Profile -->
      <div class="flex items-center space-x-3">
        <!-- Language Switch -->
        <button
          @click="toggleLanguage"
          class="px-2 py-1 text-sm bg-blue-500 hover:bg-blue-700 rounded-md transition"
        >
          {{ currentLang }}
        </button>

        <!-- Profile -->
        <button
          @click="go('/user/profile')"
          class="flex items-center justify-center p-2 rounded-full hover:bg-blue-700 transition"
        >
          <User class="w-5 h-5 text-white" />
        </button>
      </div>
    </header>

    <!-- Sidebar -->
    <transition name="slide">
      <aside
        v-if="menuOpen"
        class="fixed top-[64px] left-0 h-[calc(100%-64px)] w-64 bg-white shadow-lg z-40 flex flex-col justify-between"
      >
        <div>
          <!-- Navigation -->
          <nav class="mt-4 space-y-3 text-gray-700">
            <button
              v-for="link in links"
              :key="link.path"
              @click="go(link.path)"
              class="flex items-center space-x-3 w-full text-left py-2 px-3 rounded-md transition"
              :class="isActive(link.path)
                ? 'bg-blue-100 text-blue-700 font-semibold'
                : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600'"
            >
              <component :is="link.icon" class="w-5 h-5 text-blue-500" />
              <span>{{ getLabel(link.label) }}</span>
            </button>
          </nav>
        </div>

        <!-- Bottom: User + Logout -->
        <button
          @click="logout"
          class="border-t w-full p-4 flex items-center justify-between text-gray-700 hover:bg-gray-100 transition"
        >
          <div class="flex items-center space-x-2">
            <LogOut class="w-5 h-5 text-red-500" />
            <span>{{ $page.props.auth.user.name }}</span>
          </div>
        </button>
      </aside>
    </transition>

    <!-- Overlay when sidebar open -->
    <div
      v-if="menuOpen"
      class="fixed inset-0 bg-black/40 z-30"
      @click="menuOpen = false"
    ></div>

    <!-- Main Content -->
    <main class="flex-1 pt-16 px-4 md:px-6 overflow-y-auto">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { router } from "@inertiajs/vue3"
import {
  Menu,
  User,
  LogOut,
  Home,
  Bolt,
  Ticket,
  Wallet,
  Receipt,
} from "lucide-vue-next"

const menuOpen = ref(false)
const currentLang = ref("EN")

// تبديل اللغة
const toggleLanguage = () => {
  const newLang = currentLang.value === "EN" ? "AR" : "EN"
  currentLang.value = newLang

  // لإرسال تغيير اللغة للـBackend (إذا أردت لاحقًا)
  router.post("/set-language", { locale: newLang }, {
    preserveState: true,
    preserveScroll: true
  })
}

// الانتقال بين الصفحات
const go = (path) => {
  menuOpen.value = false
  router.visit(path)
}

// زر الخروج
const logout = () => {
  router.post("/logout")
}

// روابط Sidebar مع أيقونات
const links = [
  { path: "/user/dashboard", label: "dashboard", icon: Home },
  { path: "/user/topup", label: "topups", icon: Bolt },
  { path: "/user/vouchers", label: "vouchers", icon: Ticket },
  { path: "/user/transactions", label: "transactions", icon: Receipt },
  { path: "/user/wallet", label: "wallet", icon: Wallet },
  { path: "/user/profile", label: "profile", icon: User },
]

// تحديد الرابط النشط
const isActive = (path) => window.location.pathname === path

// ترجمة بسيطة بدون Middleware
const getLabel = (key) => {
  const labels = {
    dashboard: currentLang.value === "AR" ? "لوحة التحكم" : "Dashboard",
    topups: currentLang.value === "AR" ? "شحن الرصيد" : "Topups",
    vouchers: currentLang.value === "AR" ? "قسائم" : "Vouchers",
    transactions: currentLang.value === "AR" ? "المعاملات" : "Transactions",
    wallet: currentLang.value === "AR" ? "المحفظة" : "Wallet",
    profile: currentLang.value === "AR" ? "الملف الشخصي" : "Profile",
  }
  return labels[key] || key
}
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}
.slide-enter-from,
.slide-leave-to {
  transform: translateX(-100%);
}
</style>