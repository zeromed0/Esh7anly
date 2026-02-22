<template>
  <AdminLayout>
    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">

      <h1 class="text-2xl font-semibold text-gray-800 mb-4 sm:mb-6">Manage Orders</h1>

      <!-- بحث / فلترة -->
      <div class="flex flex-col sm:flex-row gap-2 mb-4 items-start sm:items-center">
        <input v-model="search" @input="filterOrders" placeholder="بحث..." class="border p-2 rounded w-full sm:w-1/3"/>
        <select v-model="statusFilter" @change="filterOrders" class="border p-2 rounded w-full sm:w-1/4">
          <option value="">الكل</option>
          <option value="pending">Pending</option>
          <option value="completed">Completed</option>
          <option value="rejected">Rejected</option>
        </select>
        <button @click="reloadOrders" class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition">
          تحديث
        </button>
      </div>

      <div v-if="filteredOrders.length" class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="w-full text-sm text-gray-700 border-t border-gray-200">
          <thead class="bg-gray-100">
            <tr class="text-left">
              <th class="px-2 py-2">#</th>
              <th class="px-2 py-2">User</th>
              <th class="px-2 py-2">Email</th>
              <th class="px-2 py-2">Game</th>
              <th class="px-2 py-2">Offer</th>
              <th class="px-2 py-2">Player ID</th>
              <th class="px-2 py-2 text-right">Amount</th>
              <th class="px-2 py-2 text-center">Status</th>
              <th class="px-2 py-2 text-center">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(order, index) in filteredOrders" :key="order.id" class="border-b hover:bg-gray-50 transition">
              <td class="px-2 py-2">{{ index + 1 }}</td>
              <td class="px-2 py-2">{{ order.user?.name || 'N/A' }}</td>
              <td class="px-2 py-2">{{ order.user?.email || 'N/A' }}</td>
              <td class="px-2 py-2">{{ order.game?.name || 'N/A' }}</td>
              <td class="px-2 py-2">{{ order.offer?.name || order.offer_name || 'N/A' }}</td>
              <td class="px-2 py-2 font-mono text-xs">{{ order.player_id }}</td>
              <td class="px-2 py-2 text-right text-blue-700 font-semibold">
                {{ formatCurrency(order.total_price || order.price) }}
              </td>
              <td class="px-2 py-2 text-center">
                <span
                  class="px-3 py-1 rounded-full text-xs font-semibold"
                  :class="{
                    'bg-yellow-100 text-yellow-700': order.status === 'pending',
                    'bg-green-100 text-green-700': order.status === 'completed',
                    'bg-red-100 text-red-700': order.status === 'rejected',
                  }"
                >
                  {{ order.status }}
                </span>
              </td>
              <td class="px-2 py-2 text-center space-x-1 sm:space-x-2 flex flex-col sm:flex-row justify-center">
                <button
                  @click="update(order.id, 'completed', order.status)"
                  :disabled="order.status !== 'pending'"
                  class="px-2 py-1 rounded bg-green-600 text-white hover:bg-green-700 disabled:opacity-50 text-xs sm:text-sm"
                >
                  Complete
                </button>

                <button
                  @click="update(order.id, 'rejected', order.status)"
                  :disabled="order.status !== 'pending'"
                  class="px-2 py-1 rounded bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 text-xs sm:text-sm"
                >
                  Reject
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="text-center text-gray-500 mt-10">
        No orders available.
      </div>

      <p v-if="message" class="text-center mt-4 font-medium transition" :class="messageType">
        {{ message }}
      </p>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ orders: Array })

const search = ref('')
const statusFilter = ref('')
const orders = ref([...props.orders])
const filteredOrders = ref([...orders.value])

const message = ref('')
const messageType = ref('text-green-600')

const update = (id, status, currentStatus) => {
  if (currentStatus === 'completed' || currentStatus === 'rejected') {
    message.value = '⚠️ This order can no longer be updated.'
    messageType.value = 'text-yellow-600'
    return
  }

  if (!confirm('Are you sure you want to mark this order as ' + status + '?')) return

  const form = useForm({ status })
  form.post('/admin/orders/' + id + '/status', {
    preserveScroll: true,
    onSuccess: () => {
      message.value = status === 'completed' ? '✅ Order completed.' : '⚠️ Order rejected and refunded.'
      messageType.value = status === 'completed' ? 'text-green-600' : 'text-yellow-600'
      setTimeout(() => (message.value = ''), 3000)
      reloadOrders()
    },
    onError: () => {
      message.value = '❌ Failed to update order status.'
      messageType.value = 'text-red-600'
    }
  })
}

const formatCurrency = (value) =>
  new Intl.NumberFormat('en-US', { style: 'currency', currency: 'MRU' }).format(value || 0)

const filterOrders = () => {
  filteredOrders.value = orders.value.filter(o => {
    const matchesSearch = Object.values(o).some(val => String(val).toLowerCase().includes(search.value.toLowerCase()))
    const matchesStatus = !statusFilter.value || o.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
}

const reloadOrders = () => {
  router.reload()
}
</script>

<style scoped>
body { direction: rtl; }
</style>