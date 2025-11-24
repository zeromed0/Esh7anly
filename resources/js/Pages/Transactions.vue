<template>
  <UserLayout :user="user">
    <div
      class="p-4 max-w-4xl mx-auto"
      @touchstart="startPull"
      @touchmove="movePull"
      @touchend="endPull"
      :style="pullTransform"
    >
      <!-- Refresh Indicator -->
      <div v-if="isRefreshing" class="text-center text-blue-600 font-medium mb-2">
        🔄 Refreshing...
      </div>

      <h1 class="text-xl font-bold text-blue-700 mb-4">Transactions</h1>

      <!-- FILTER BUTTONS -->
      <div class="flex space-x-2 mb-5 overflow-x-auto pb-1">
        <button
          v-for="f in filters"
          :key="f.value"
          @click="activeFilter = f.value"
          class="px-4 py-2 rounded-full text-sm font-medium border transition whitespace-nowrap"
          :class="
            activeFilter === f.value
              ? 'bg-blue-600 text-white border-blue-600'
              : 'bg-white text-gray-600 border-gray-300'
          "
        >
          {{ f.label }}
        </button>
      </div>

      <!-- NO DATA -->
      <p v-if="filteredTransactions.length === 0" class="text-center text-gray-500 mt-10">
        No transactions found.
      </p>

      <!-- LIST -->
      <div class="space-y-3">
        <div
          v-for="tx in filteredTransactions"
          :key="tx.id"
          class="bg-white shadow-sm rounded-xl p-4 border relative"
        >
          <!-- Color Badge -->
          <div
            class="absolute top-3 right-3 w-3 h-3 rounded-full"
            :class="statusCircle(tx.status)"
          ></div>

          <!-- HEADER -->
          <div class="flex justify-between items-center mb-1">
            <p class="font-semibold text-gray-800 text-sm">
              {{ getTypeLabel(tx.type) }} | <span >ID: {{ tx.id }}</span>
            </p>

            <span class="text-xs font-semibold px-2 py-1 rounded-full" :class="statusClass(tx.status)">
              {{ capitalize(tx.status) }}
            </span>
          </div>

          <!-- BASIC INFO -->
          <p class="text-sm text-gray-700 font-medium truncate">
            {{ tx.game_name }} — {{ tx.offer_name }}
          </p>

          <p class="text-sm mt-1 font-bold text-blue-600">{{ formatCurrency(tx.total_price) }} | <span > <strong>Quantity</strong>: {{ tx.quantity }}</span> </p>

          <!-- DETAILS -->
          <details class="mt-2 bg-gray-50 rounded-lg p-3 text-sm">
            <summary class="cursor-pointer font-semibold text-gray-800">Details</summary>

            <div class="mt-2 space-y-2">
              <p><strong>Balance After:</strong> {{ formatCurrency(tx.balance_after) }}</p>
              <p><strong>Date:</strong> {{ formatDate(tx.created_at) }}</p>

              <template v-if="tx.type === 'topup'">
                <p><strong>Player ID:</strong> {{ tx.player_id }}</p>
              </template>

              <template v-else-if="tx.type === 'voucher'">
                <ul class="space-y-1 mt-1">
                  <li
                    v-for="code in tx.codes"
                    :key="code"
                    class="flex justify-between items-center bg-white p-2 rounded border"
                  >
                    <span class="font-mono">{{ code }}</span>
                    <button @click="copy(code)" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">
                      📋 Copy
                    </button>
                  </li>
                </ul>
              </template>

              <template v-else-if="tx.type === 'wallet'">
                <p>Wallet recharge by admin.</p>
              </template>
            </div>
          </details>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({ user: Object, transactions: Array });

const filters = [
  { label: "All", value: "all" },
  { label: "Completed", value: "completed" },
  { label: "Pending", value: "pending" },
  { label: "Rejected", value: "rejected" },
];

const activeFilter = ref("all");

const filteredTransactions = computed(() =>
  activeFilter.value === "all"
    ? props.transactions
    : props.transactions.filter((t) => t.status === activeFilter.value)
);

const getTypeLabel = (type) =>
  type === "topup" ? "🔼 Top-up" : type === "voucher" ? "🎟️ Voucher" : type === "wallet" ? "💰 Wallet" : type;

const statusClass = (status) => ({
  "bg-green-100 text-green-700": status === "completed",
  "bg-yellow-100 text-yellow-700": status === "pending",
  "bg-red-100 text-red-700": status === "rejected",
});

const statusCircle = (status) => ({
  "bg-green-500": status === "completed",
  "bg-yellow-500": status === "pending",
  "bg-red-500": status === "rejected",
});

const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1);
const copy = (t) => navigator.clipboard.writeText(t);
const formatCurrency = (v) => new Intl.NumberFormat("en-US", { style: "currency", currency: "MRU" }).format(v || 0);
const formatDate = (date) => new Date(date).toLocaleString("fr-MR", { timeZone: "Africa/Nouakchott" });

// Pull-to-refresh
const pullStart = ref(0);
const pullDistance = ref(0);
const isRefreshing = ref(false);

// طريقة كتابة transform بشكل أبسط
const pullTransform = computed(() => ({
  transform: "translateY(" + pullDistance.value + "px)",
  transition: pullDistance.value === 0 ? "transform 0.2s ease-out" : "none",
}));

const startPull = (e) => { pullStart.value = e.touches[0].clientY; };
const movePull = (e) => {
  const distance = e.touches[0].clientY - pullStart.value;
  if (distance > 0 && distance < 120) pullDistance.value = distance;
};
const endPull = () => {
  if (pullDistance.value > 80) {
    isRefreshing.value = true;
    setTimeout(() => router.visit("/user/transactions", { preserveScroll: true }), 300);
  }
  pullDistance.value = 0;
};
</script>

<style scoped>
details summary::-webkit-details-marker { display: none; }
</style>