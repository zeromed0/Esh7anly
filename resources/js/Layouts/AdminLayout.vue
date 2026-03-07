
<template>
  <div class="min-h-screen bg-gray-100">

    <!-- 🔵 Top Bar -->
    <header class="bg-gradient-to-l from-blue-700 to-blue-600 text-white shadow-md">
      <div class="max-w-7xl mx-auto px-6">

        <div class="flex items-center justify-between h-16">

          <!-- Logo -->
          <div class="flex items-center space-x-2 cursor-pointer" @click="goTo('/admin/dashboard')">
            <img src="/images/logo1.png" class="h-9 w-auto" />
            <span class="text-lg font-semibold hidden sm:block">
              Admin Panel
            </span>
          </div>

          <!-- Admin -->
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-semibold">
              {{ $page.props.auth.user.name.charAt(0) }}
            </div>
            <span class="text-sm hidden sm:block">
              {{ $page.props.auth.user.name }}
            </span>
          </div>

        </div>

      </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-6 py-8">
      <slot />
    </main>

  </div>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const currentUrl = computed(() => page.url)

const NavLink = {
  props: ['label', 'url'],
  setup(props) {
    const isActive = computed(() => currentUrl.value.startsWith(props.url))
    const go = () => router.visit(props.url)
    return { isActive, go }
  },
  template: `
    <button 
      @click="go"
      class="relative pb-1 whitespace-nowrap transition-all duration-200"
      :class="isActive 
        ? 'text-white'
        : 'text-white/80 hover:text-white'"
    >
      {{ label }}
      <span 
        v-if="isActive"
        class="absolute left-0 right-0 -bottom-1 h-[2px] bg-white rounded-full"
      ></span>
    </button>
  `
}
</script>

<style scoped>
body {
  direction: rtl;
  font-family: "Inter", "Cairo", sans-serif;
}
</style>