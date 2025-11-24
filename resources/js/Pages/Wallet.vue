<template>
  <UserLayout :user="user">
    <div
      class="p-5 md:p-8 max-w-3xl mx-auto w-full space-y-8"
      @touchstart="startPull"
      @touchmove="movePull"
      @touchend="endPull"
      :style="pullStyle"
    >
      <!-- Refresh Indicator -->
      <div v-if="isRefreshing" class="text-center text-blue-600 font-medium mb-2">
        🔄 Refreshing...
      </div>

      <!-- Wallet Card -->
      <div
        class="relative bg-gradient-to-r from-blue-700 via-blue-600 to-blue-400 text-white rounded-2xl shadow-2xl p-6 overflow-hidden"
      >
        <div class="absolute inset-0 bg-white/10 blur-2xl"></div>
        <div class="relative">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">My Wallet</h2>
            <i class="fas fa-wallet text-2xl"></i>
          </div>
          <p class="text-3xl md:text-4xl font-bold truncate">{{ formatCurrency(user.balance) }}</p>
          <p class="text-sm opacity-90 mt-1">Available Balance</p>
          <div class="mt-4 text-xs opacity-80">Card Holder: {{ user.name }}</div>
        </div>
      </div>

      <!-- Redeem Voucher -->
      <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Redeem Voucher</h3>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
          <input
            v-model="code"
            type="text"
            placeholder="Enter voucher code..."
            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
          />
          <button
            @click="redeem"
            class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-lg transition"
          >
            Redeem
          </button>
        </div>
        <p
          v-if="message"
          :class="messageType"
          class="mt-3 text-sm font-medium text-center"
        >
          {{ message }}
        </p>
      </div>

      <!-- Voucher History -->
      <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Voucher History</h3>

        <template v-if="usedVouchers.length">
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-gray-700 border-t border-gray-200">
              <thead>
                <tr class="text-left border-b bg-gray-50">
                  <th class="py-2 px-3">Code</th>
                  <th class="py-2 px-3 text-right">Amount</th>
                  <th class="py-2 px-3 text-right">Used At</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="voucher in usedVouchers"
                  :key="voucher.id"
                  class="border-b hover:bg-gray-50 transition"
                >
                  <td class="py-2 px-3 font-mono text-xs truncate">{{ voucher.code }}</td>
                  <td class="py-2 px-3 text-right text-green-600 font-semibold">
                    {{ formatCurrency(voucher.amount) }}
                  </td>
                  <td class="py-2 px-3 text-right text-gray-500">
                    {{ formatDate(voucher.used_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </template>

        <div v-else class="text-center text-gray-400 mt-4">
          No vouchers redeemed yet.
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, computed } from "vue"
import UserLayout from '@/Layouts/UserLayout.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  user: Object,
  usedVouchers: Array,
})

const code = ref('')
const message = ref('')
const messageType = ref('text-green-600')

const redeem = async () => {
  if (!code.value.trim()) {
    message.value = 'Please enter a valid code.'
    messageType.value = 'text-red-600'
    return
  }

  await router.post(
    '/user/wallet/redeem',
    { code: code.value },
    {
      onSuccess: (page) => {
        const redeemedVoucher = page.props.redeemedVoucher
        if (redeemedVoucher) {
          props.user.balance += redeemedVoucher.amount
          props.usedVouchers.unshift(redeemedVoucher)
          message.value = 'Voucher redeemed successfully!'
          messageType.value = 'text-green-600'
          code.value = ''
        }
      },
      onError: () => {
        message.value = 'Invalid or already used voucher.'
        messageType.value = 'text-red-600'
      },
    }
  )
}

// Formatting
const formatCurrency = (value) =>
  new Intl.NumberFormat("en-US", { style: "currency", currency: "MRU" }).format(value || 0)

const formatDate = (date) => {
  if (!date) return ""
  return new Date(date).toLocaleString("en-GB", {
    year: "numeric", month: "short", day: "numeric",
    hour: "2-digit", minute: "2-digit"
  })
}

// Pull-to-refresh
const pullStart = ref(0)
const pullDistance = ref(0)
const isRefreshing = ref(false)

const pullStyle = computed(() => ({
  transform: "translateY(" + pullDistance.value + "px)",
  transition: pullDistance.value === 0 ? "transform 0.2s ease-out" : "none",
}))

const startPull = (e) => { pullStart.value = e.touches[0].clientY }
const movePull = (e) => {
  const distance = e.touches[0].clientY - pullStart.value
  if (distance > 0 && distance < 120) pullDistance.value = distance
}
const endPull = () => {
  if (pullDistance.value > 80) {
    isRefreshing.value = true
    setTimeout(() => router.visit("/user/wallet", { preserveScroll: true }), 300)
  }
  pullDistance.value = 0
}
</script>

<style scoped>
/* Remove default details marker */
details summary::-webkit-details-marker { display: none; }
</style>