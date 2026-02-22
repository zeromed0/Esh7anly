<template>
  <AdminLayout>
    <!-- ترحيب الأدمن -->
    <div class="mt-6 mb-6 px-6 text-center sm:text-right">
      <h2 class="text-2xl md:text-3xl font-bold text-gray-700">
        مرحبا لك أيها الأدمن، {{ adminName }}
      </h2>
    </div>

    <!-- محتوى Dashboard -->
    <main class="px-4 sm:px-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
      <!-- Orders -->
      <button
        @click="goTo('/admin/orders')"
        class="bg-white shadow-md md:shadow-lg rounded-2xl p-5 flex flex-col items-center justify-center hover:shadow-xl hover:bg-blue-50 transition duration-300"
      >
        <ShoppingCart class="w-12 h-12 text-blue-600 mb-2" />
        <span class="text-gray-700 font-semibold text-sm md:text-base">Orders</span>
      </button>

      <!-- Top Ups -->
      <button
        @click="goTo('/admin/topups')"
        class="bg-white shadow-md md:shadow-lg rounded-2xl p-5 flex flex-col items-center justify-center hover:shadow-xl hover:bg-yellow-50 transition duration-300"
      >
        <Bolt class="w-12 h-12 text-yellow-500 mb-2" />
        <span class="text-gray-700 font-semibold text-sm md:text-base">Top Ups</span>
      </button>

      <!-- Vouchers -->
      <button
        @click="goTo('/admin/vouchers')"
        class="bg-white shadow-md md:shadow-lg rounded-2xl p-5 flex flex-col items-center justify-center hover:shadow-xl hover:bg-green-50 transition duration-300"
      >
        <Ticket class="w-12 h-12 text-green-500 mb-2" />
        <span class="text-gray-700 font-semibold text-sm md:text-base">Vouchers</span>
      </button>

      <!-- Wallet Vouchers -->
      <button
        @click="goTo('/admin/wallet-vouchers')"
        class="bg-white shadow-md md:shadow-lg rounded-2xl p-5 flex flex-col items-center justify-center hover:shadow-xl hover:bg-purple-50 transition duration-300"
      >
        <Wallet class="w-12 h-12 text-purple-500 mb-2" />
        <span class="text-gray-700 font-semibold text-sm md:text-base">Wallet Vouchers</span>
      </button>

      <!-- Users -->
      <button
        @click="goTo('/admin/users')"
        class="bg-white shadow-md md:shadow-lg rounded-2xl p-5 flex flex-col items-center justify-center hover:shadow-xl hover:bg-red-50 transition duration-300"
      >
        <User class="w-12 h-12 text-red-500 mb-2" />
        <span class="text-gray-700 font-semibold text-sm md:text-base">Users</span>
      </button>
    </main>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ShoppingCart, Bolt, Ticket, Wallet, User } from 'lucide-vue-next'

const adminName = ref('Admin')

const goTo = (url) => {
  router.visit(url)
}
</script>

<style scoped>
body {
  direction: rtl;
}

/* تحسين حجم الأزرار على الهاتف */
button {
  font-weight: bold;
  font-size: 14px;
}

@media (min-width: 768px) {
  button {
    font-size: 16px;
  }
}

/* الظلال والتحولات */
button:hover {
  transform: translateY(-2px);
}
</style>