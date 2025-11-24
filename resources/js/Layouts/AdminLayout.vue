<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Header -->
    <header
      class="fixed top-0 left-0 right-0 z-50 bg-blue-600 text-white shadow-md flex items-center justify-between px-6 py-4"
    >
      <!-- Left: Logo + Title -->
      <div class="flex items-center space-x-3">
        <img src="/images/logo.png" alt="Logo" class="h-12 w-auto" />
        <h1 class="text-xl font-semibold">لوحة تحكم الأدمن</h1>
      </div>

      <!-- Right: Admin Name -->
      <div class="text-white font-medium">
        {{ $page.props.auth.user.name }}
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 pt-20 px-6">
      <slot />
    </main>
  </div>
</template>

<script setup>
</script>

<style scoped>
body {
  font-family: "Inter", "Cairo", sans-serif;
}
</style>