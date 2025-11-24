<template>
  <UserLayout :user="user">
    <div class="p-4 max-w-5xl mx-auto w-full">

      <!-- Wallet + Transactions -->
      <section class="grid grid-cols-1 gap-4 mb-8">

        <!-- Wallet -->
        <div
          class="bg-gradient-to-r from-blue-600 to-blue-400 text-white p-5 rounded-2xl shadow-md active:scale-95 transition relative overflow-hidden"
          @click="router.visit('/user/wallet')"
        >
          <div class="absolute inset-0 bg-white/10 blur-xl"></div>
          <div class="relative">
            <div class="flex justify-between items-center">
              <h2 class="text-xs uppercase tracking-widest opacity-90">Wallet Balance</h2>
              <i class="fas fa-wallet text-2xl opacity-90"></i>
            </div>
            <p class="text-2xl font-bold mt-2">{{ formatCurrency(user.balance) }}</p>
            <p class="text-[11px] opacity-80 mt-1">2Sh7anli Virtual Card</p>
          </div>
        </div>

      </section>

      <!-- Search -->
      <div class="mb-6">
        <input
          v-model="search"
          type="text"
          placeholder="Search games…"
          class="w-full p-3 text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-400"
        >
      </div>

      <!-- TOPUPS -->
      <section class="mb-10">
        <div class="flex justify-between items-center mb-3">
          <h2 class="text-lg font-semibold text-gray-700">Popular Top Ups</h2>
          <button
            @click="router.visit('/user/topup')"
            class="text-blue-600 hover:text-blue-800 text-xs font-medium"
          >
            View All →
          </button>
        </div>

        <div class="flex overflow-x-auto space-x-4 pb-1">
          <div
            v-for="game in filteredTopups"
            :key="game.id"
            @click="router.visit('/user/topup?game=' + game.id)"
            class="bg-white rounded-2xl shadow-sm hover:shadow-md p-3 min-w-[110px] cursor-pointer transition flex flex-col items-center"
          >
            <img :src="game.image" class="w-14 h-14 rounded-full object-cover mb-2" />
            <p class="text-xs font-medium text-gray-700 text-center truncate w-full">{{ game.name }}</p>
          </div>
        </div>
      </section>

      <!-- VOUCHERS -->
      <section class="mb-10">
        <div class="flex justify-between items-center mb-3">
          <h2 class="text-lg font-semibold text-gray-700">Popular Vouchers</h2>
          <button
            @click="router.visit('/user/vouchers')"
            class="text-blue-600 hover:text-blue-800 text-xs font-medium"
          >
            View All →
          </button>
        </div>

        <div class="flex overflow-x-auto space-x-4 pb-1">
          <div
            v-for="game in filteredVouchers"
            :key="game.id"
            @click="router.visit('/user/vouchers?game=' + game.id)"
            class="bg-white rounded-2xl shadow-sm hover:shadow-md p-3 min-w-[110px] cursor-pointer transition flex flex-col items-center"
          >
            <img :src="game.image" class="w-14 h-14 rounded-full object-cover mb-2" />
            <p class="text-xs font-medium text-gray-700 text-center truncate w-full">{{ game.name }}</p>
          </div>
        </div>
      </section>

      <!-- Recent Orders -->
      <section class="mt-10">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold text-gray-700">Recent Orders</h2>
          <button
            @click="router.visit('/user/transactions')"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
          >
            View All →
          </button>
        </div>

        <div
          v-for="order in recentOrders"
          :key="order.id"
          @click="router.visit('/user/transactions')"
          class="bg-white p-4 rounded-xl shadow-sm mb-3 border-l-4 cursor-pointer active:scale-[0.98] transition"
          :class="order.status === 'completed'
            ? 'border-green-500'
            : order.status === 'pending'
            ? 'border-yellow-500'
            : 'border-red-500'"
        >
          <div class="flex justify-between">
            <p class="font-semibold text-gray-700 text-sm">{{ order.game_name }}</p>
            <span
              class="text-xs font-bold"
              :class="order.status === 'completed'
                ? 'text-green-600'
                : order.status === 'pending'
                ? 'text-yellow-600'
                : 'text-red-600'"
            >
              {{ order.status }}
            </span>
          </div>

          <p class="text-xs mt-1 text-gray-500">
            {{ order.offer_name }} • {{ order.type }}
          </p>

          <p class="text-xs mt-1 font-medium text-gray-700">
            Amount: {{ formatCurrency(order.total_price) }}
          </p>

          <p class="text-xs mt-1 text-gray-400">
            {{ order.date }}
          </p>
        </div>
      </section>

    </div>
  </UserLayout>
</template>

<script setup>
import UserLayout from '@/Layouts/UserLayout.vue'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  user: Object,
  games: Array,
  recentOrders: Array,
  topups: Array,
  vouchers: Array,
})

const search = ref('')

const filteredTopups = computed(() =>
  props.topups.filter(g =>
    g.name.toLowerCase().includes(search.value.toLowerCase())
  )
)

const filteredVouchers = computed(() =>
  props.vouchers.filter(g =>
    g.name.toLowerCase().includes(search.value.toLowerCase())
  )
)

const formatCurrency = (value) =>
  new Intl.NumberFormat("en-US", { style: "currency", currency: "MRU" }).format(value || 0)
</script>