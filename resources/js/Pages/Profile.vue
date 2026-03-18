
<template>
  <UserLayout :user="user">
    <div class="min-h-screen flex justify-center p-6 bg-gradient-to-br from-blue-50 via-white to-blue-100">

      <div class="w-full max-w-3xl space-y-6">

        <!-- Profile Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 text-center">
          <div class="flex justify-center mb-4">
            <div
              class="w-24 h-24 flex items-center justify-center rounded-full text-4xl border-4"
              :class="user.is_banned ? 'border-red-500 bg-red-100' : user.is_admin ? 'border-gray-700 bg-gray-200' : 'border-blue-400 bg-blue-100'"
            >
              <span v-if="user.is_admin">🛡️</span>
              <span v-else-if="user.is_banned">🚫</span>
              <span v-else>👤</span>
            </div>
          </div>

          <h2 class="text-2xl font-bold text-gray-800">{{ user.name }}</h2>
          <p class="text-gray-500 text-sm">
            {{ user.email }} 
            <span v-if="user.email_verified_at" class="text-green-600 font-semibold">(Verified)</span>
          </p>
          <p class="text-gray-500 text-sm">
            Phone: {{ user.phone || '-' }} 
            <span v-if="user.phone_verified_at" class="text-green-600 font-semibold">(Verified)</span>
          </p>
          <p class="text-xs text-gray-400 mt-1">User ID: {{ user.id }}</p>
          <p class="text-xs text-gray-400">Joined: {{ formatDate(user.created_at) }}</p>

          <!-- Verification Badge -->
          <div class="mt-3 flex justify-center space-x-2">
            <span v-if="user.verification_status === 'verified'" class="bg-green-100 text-green-700 px-3 py-1 rounded text-xs font-semibold">
              Account Verified
            </span>
            <span v-else-if="user.verification_status === 'pending'" class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded text-xs font-semibold">
              Verification Pending
            </span>
            <span v-else class="bg-gray-100 text-gray-600 px-3 py-1 rounded text-xs font-semibold">
              Not Verified
            </span>
          </div>

          <div class="mt-2">
            <p class="text-gray-500 text-sm">National ID: {{ user.national_id || '-' }}</p>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4">
          <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-xs text-gray-500">Wallet</p>
            <p class="text-lg font-bold text-green-600">{{ balance }} MRU</p>
          </div>

          <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-xs text-gray-500">Transactions</p>
            <p class="text-lg font-bold text-blue-600">{{ transactionsCount }}</p>
          </div>

          <div class="bg-white rounded-xl shadow p-4 text-center">
            <p class="text-xs text-gray-500">Status</p>
            <p class="text-lg font-bold text-gray-800">{{ user.is_banned ? 'Banned' : 'Active' }}</p>
          </div>
        </div>

        <!-- Message for Unverified Users -->
        <div v-if="user.verification_status !== 'verified'" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded text-center">
          <p class="text-sm text-yellow-800">
            To unlock full features, please verify your account.
            <button
              @click="goToVerification"
              class="underline font-semibold text-yellow-900 ml-1 hover:text-yellow-700"
            >
              Click here to verify
            </button>
          </p>
        </div>

        <!-- Messages -->
        <div v-if="message" class="text-center text-sm" :class="messageClass">
          {{ message }}
        </div>

      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'

const props = defineProps({
  user: { type: Object, required: true },
  balance: { type: Number, required: true },
  transactionsCount: { type: Number, required: true }
})

const message = ref('')
const messageClass = ref('text-green-600')

// التاريخ بشكل جميل
const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-GB', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// الانتقال إلى صفحة التوثيق
const goToVerification = () => {
  router.visit('/user/verification')
}
</script>

<style scoped>
</style>