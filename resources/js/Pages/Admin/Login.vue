<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-800 to-blue-500 px-4">
    <div class="bg-white shadow-2xl rounded-3xl p-10 w-full max-w-md">
      
      <!-- Header -->
      <div class="flex flex-col items-center mb-8">
        <div class="w-20 h-20 rounded-full bg-blue-600 flex items-center justify-center shadow-lg mb-4">
          <img src="/logo.png" alt="Admin Logo" class="w-12 h-12" />
        </div>
        <h1 class="text-3xl font-extrabold text-gray-800 mb-1">تسجيل دخول الأدمن</h1>
        <p class="text-gray-500 text-sm text-center">يرجى إدخال بيانات الدخول الخاصة بك للوصول إلى لوحة التحكم</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Email -->
        <div>
          <label for="email" class="block text-gray-700 font-medium mb-2">البريد الإلكتروني</label>
          <input
            v-model="form.email"
            type="email"
            id="email"
            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
            placeholder="example@email.com"
            required
          />
          <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-gray-700 font-medium mb-2">كلمة المرور</label>
          <input
            v-model="form.password"
            type="password"
            id="password"
            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
            placeholder="••••••••"
            required
          />
          <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition flex justify-center items-center"
          :disabled="form.processing"
        >
          <span v-if="!form.processing">تسجيل الدخول</span>
          <span v-else class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            جارٍ الدخول...
          </span>
        </button>
      </form>

      <!-- Footer -->
      <div class="mt-6 text-center text-gray-400 text-xs">
        © 2025 جميع الحقوق محفوظة
      </div>

    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
})

const submit = () => {
  form.post('/admin/login')
}
</script>

<style scoped>
body {
  direction: rtl;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
</style>