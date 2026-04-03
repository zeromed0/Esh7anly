
<template>
  <AdminLayout>
    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen relative">
      <h1 class="text-2xl font-semibold text-gray-800 mb-4 sm:mb-6">Manage Orders</h1>

      <!-- Toast Notification -->
      <transition name="fade">
        <div v-if="toast.show"
             class="fixed top-6 right-6 z-50 px-5 py-3 rounded-xl shadow-lg text-white text-sm font-semibold"
             :class="toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'">
          {{ toast.message }}
        </div>
      </transition>

      <!-- Confirmation Modal -->
      <div v-if="confirmModal.show"
           class="fixed inset-0 bg-black/40 flex items-center justify-center z-40">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-80 text-center">
          <h3 class="text-lg font-semibold mb-3">Confirm Action</h3>
          <p class="text-gray-600 mb-5">
            Are you sure you want to
            <span class="font-semibold"
                  :class="confirmModal.status === 'completed' ? 'text-green-600' : 'text-red-600'">
              {{ confirmModal.status === 'completed' ? 'APPROVE' : 'REJECT' }}
            </span>
            this order?
          </p>
          <div class="flex justify-center gap-3">
            <button @click="confirmModal.show = false"
                    class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
              Cancel
            </button>
            <button @click="confirmAction"
                    class="px-4 py-2 rounded-lg text-white"
                    :class="confirmModal.status === 'completed'
                      ? 'bg-green-600 hover:bg-green-700'
                      : 'bg-red-600 hover:bg-red-700'">
              Confirm
            </button>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex flex-col sm:flex-row gap-2 mb-4 items-start sm:items-center">
        <input v-model="search" @input="filterOrders"
               placeholder="بحث برقم الطلب أو الإيميل..."
               class="border p-2 rounded w-full sm:w-1/3" />

        <select v-model="statusFilter" @change="filterOrders"
                class="border p-2 rounded w-full sm:w-1/4">
          <option value="">الكل</option>
          <option value="pending">Pending</option>
          <option value="completed">Completed</option>
          <option value="rejected">Rejected</option>
        </select>

        <select v-model="typeFilter" @change="filterOrders"
                class="border p-2 rounded w-full sm:w-1/4">
          <option value="">كل الأنواع</option>
          <option value="topup">TopUp</option>
          <option value="voucher">Voucher</option>
        </select>

        <button @click="reloadOrders"
                class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition">
          تحديث
        </button>
      </div>

      <div v-if="filteredOrders.length"
           class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="w-full text-sm text-gray-700 border-t border-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-2 py-2">#</th>
              <th class="px-2 py-2">User</th>
              <th class="px-2 py-2">Game</th>
              <th class="px-2 py-2">Offer</th>
              <th class="px-2 py-2">Player ID</th>
              <th class="px-2 py-2 text-right">Amount</th>
              <th class="px-2 py-2 text-center">Type</th>
              <th class="px-2 py-2 text-center">Status</th>
              <th class="px-2 py-2 text-center">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="order in filteredOrders"
                :key="order.id"
                class="border-b hover:bg-gray-50 transition">
              <td class="px-2 py-2">{{ order.id }}</td>
              <td class="px-2 py-2">{{ order.user?.name || 'N/A' }}</td>
              <td class="px-2 py-2">{{ order.game?.name || 'N/A' }}</td>
              <td class="px-2 py-2">{{ order.offer?.name || order.offer_name || 'N/A' }}</td>
              <td class="px-2 py-2 font-mono text-xs">{{ order.player_id }}</td>
              <td class="px-2 py-2 text-right text-blue-700 font-semibold">
                {{ formatCurrency(order.total_price || order.price) }}
              </td>
              <td class="px-2 py-2 text-center">
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
                  {{ order.type || 'topup' }}
                </span>
              </td>
              <td class="px-2 py-2 text-center">
                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                      :class="{
                        'bg-yellow-100 text-yellow-700': order.status === 'pending',
                        'bg-green-100 text-green-700': order.status === 'completed',
                        'bg-red-100 text-red-700': order.status === 'rejected',
                      }">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-2 py-2 text-center flex flex-col sm:flex-row gap-1 justify-center">
                <input v-if="order.type === 'topup' && order.status === 'pending'"
                       v-model="order.messageInput"
                       type="text"
                       placeholder="Enter message/code"
                       class="border px-2 py-1 rounded text-xs" />

                <button @click="openConfirm(order, 'completed')"
                        :disabled="order.status !== 'pending'"
                        class="px-2 py-1 rounded bg-green-600 text-white hover:bg-green-700 disabled:opacity-50 text-xs">
                  Complete
                </button>

                <button @click="openConfirm(order, 'rejected')"
                        :disabled="order.status !== 'pending'"
                        class="px-2 py-1 rounded bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 text-xs">
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
    </div>
  </AdminLayout>
</template>


<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ orders: Array })

const orders = ref(props.orders.map(o => ({ ...o, messageInput: '' })))
const filteredOrders = ref([...orders.value])

const search = ref('')
const statusFilter = ref('')
const typeFilter = ref('')

const toast = ref({ show: false, message: '', type: 'success' })

const confirmModal = ref({
  show: false,
  order: null,
  status: null
})

const formatCurrency = (value) =>
  new Intl.NumberFormat('en-US', { style: 'currency', currency: 'MRU' }).format(value || 0)

const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => toast.value.show = false, 2000)
}

const filterOrders = () => {
  filteredOrders.value = orders.value.filter(o => {
    const searchLower = search.value.toLowerCase()
    const matchesSearch = !search.value ||
      String(o.id).includes(searchLower) ||
      (o.user?.email && o.user.email.toLowerCase().includes(searchLower))

    const matchesStatus = !statusFilter.value || o.status === statusFilter.value
    const matchesType = !typeFilter.value || o.type === typeFilter.value

    return matchesSearch && matchesStatus && matchesType
  })
}

const reloadOrders = () => router.reload({ preserveScroll: true })

const openConfirm = (order, status) => {
  confirmModal.value = { show: true, order, status }
}

const confirmAction = () => {

  const { order, status } = confirmModal.value
  confirmModal.value.show = false

  // 🔴 منع التعديل إذا لم يعد Pending
  if (order.status !== 'pending') {
    showToast('This order is already finalized', 'error')
    return
  }

  // 🔴 جعل الرسالة إجبارية للحالتين
  if (!order.messageInput || order.messageInput.trim() === '') {
    showToast('يجب إدخال رسالة أو كود قبل تحديث الحالة', 'error')
    return
  }

  router.post('/admin/orders/' + order.id + '/status', {
    status: status,
    message: order.messageInput
  }, {
    preserveScroll: true,
    onSuccess: () => {

      // تحديث مباشر داخل المصفوفة
      order.status = status
      order.messageInput = ''

      const mainIndex = orders.value.findIndex(o => o.id === order.id)
      if (mainIndex !== -1) {
        orders.value[mainIndex].status = status
      }

      filterOrders()

      showToast(
        status === 'completed'
          ? 'تمت الموافقة على الطلب'
          : 'تم رفض الطلب واسترجاع الرصيد'
      )
    },
    onError: () => {
      showToast('فشل تحديث الطلب', 'error')
    }
  })
}
</script>