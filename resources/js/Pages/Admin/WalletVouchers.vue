<template>
  <AdminLayout>
    <div class="p-4 sm:p-6 bg-gray-50 min-h-screen">

      <h1 class="text-2xl font-bold mb-4 sm:mb-6 text-gray-800">أكواد المحفظة (Wallet Vouchers)</h1>

      <!-- إنشاء كود جديد -->
      <div class="mb-4 p-4 bg-white rounded shadow flex flex-col sm:flex-row gap-2 items-start sm:items-center">
        <input v-model.number="amount" type="number" min="1" placeholder="القيمة" class="border p-2 rounded w-full sm:w-1/3"/>
        <button @click="createVoucher" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
          إنشاء كود تلقائي
        </button>
      </div>

      <!-- بحث وفلترة -->
      <div class="flex flex-col sm:flex-row gap-2 mb-4 items-start sm:items-center">
        <input v-model="search" placeholder="بحث عن كود..." @input="filterVouchers" class="border p-2 rounded w-full sm:w-1/3"/>
        <select v-model="filterUsed" @change="filterVouchers" class="border p-2 rounded w-full sm:w-1/4">
          <option value="">الكل</option>
          <option value="used">مستخدم</option>
          <option value="unused">غير مستخدم</option>
        </select>
        <button @click="reloadVouchers" class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition">تحديث</button>
      </div>

      <!-- قائمة الأكواد -->
      <div class="overflow-x-auto bg-white rounded shadow p-2">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b bg-gray-50">
              <th class="px-2 py-1">الكود</th>
              <th class="px-2 py-1">القيمة</th>
              <th class="px-2 py-1">الحالة</th>
              <th class="px-2 py-1">المستخدم</th>
              <th class="px-2 py-1">أفعال</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="voucher in filteredVouchers" :key="voucher.id" class="border-b hover:bg-gray-50 transition">
              <td class="font-mono">{{ voucher.code }}</td>
              <td>{{ voucher.amount }} د.أ</td>
              <td :class="voucher.is_used ? 'text-red-600' : 'text-green-600'">{{ voucher.is_used ? 'مستخدم' : 'غير مستخدم' }}</td>
              <td>{{ voucher.used_by || '-' }}</td>
              <td class="flex gap-2">
                <button @click="deleteVoucher(voucher.id)" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 text-xs sm:text-sm">
                  حذف
                </button>
              </td>
            </tr>
            <tr v-if="filteredVouchers.length === 0">
              <td colspan="5" class="text-center text-gray-500 py-2">لا توجد أكواد حتى الآن.</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ vouchers: Array })

const vouchers = ref([...props.vouchers])
const filteredVouchers = ref([...vouchers.value])
const amount = ref('')
const search = ref('')
const filterUsed = ref('')

const createVoucher = () => {
  if(!amount.value || amount.value <= 0) { alert('الرجاء إدخال قيمة صحيحة'); return }
  const form = useForm({ amount: amount.value })
  form.post('/admin/wallet-vouchers', {
    onSuccess: () => { router.reload() }
  })
}

const deleteVoucher = (id) => {
  if(!confirm('هل تريد حذف هذا الكود؟')) return
  const form = useForm({})
  form.delete('/admin/wallet-vouchers/'+id, { onSuccess: () => router.reload() })
}

const filterVouchers = () => {
  filteredVouchers.value = vouchers.value.filter(v => {
    const matchesSearch = v.code.toLowerCase().includes(search.value.toLowerCase())
    const matchesUsed = filterUsed.value === 'used' ? v.is_used
                     : filterUsed.value === 'unused' ? !v.is_used
                     : true
    return matchesSearch && matchesUsed
  })
}

const reloadVouchers = () => { router.reload() }
</script>

<style scoped>
body { direction: rtl; }
</style>