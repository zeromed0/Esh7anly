
<template>
  <AdminLayout>

    <!-- ترحيب الأدمن -->
    <div class="mt-6 mb-8 px-6 text-center sm:text-right">
      <h2 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-wide">
        مرحبا لك أيها الأدمن، {{ adminName }}
      </h2>
    </div>

    <!-- Dashboard Grid -->
    <main class="px-4 sm:px-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">

      <!-- Orders -->
      <button
        @click="goTo('/admin/orders')"
        class="dashboard-btn hover:bg-blue-50"
      >
        <ShoppingCart class="icon text-blue-600" />
        <span>Orders</span>
      </button>

      <!-- Top Ups -->
      <button
        @click="goTo('/admin/topups')"
        class="dashboard-btn hover:bg-yellow-50"
      >
        <Bolt class="icon text-yellow-500" />
        <span>Top Ups</span>
      </button>

      <!-- Vouchers -->
      <button
        @click="goTo('/admin/vouchers')"
        class="dashboard-btn hover:bg-green-50"
      >
        <Ticket class="icon text-green-500" />
        <span>Vouchers</span>
      </button>

      <!-- Wallet Vouchers -->
      <button
        @click="goTo('/admin/wallet-vouchers')"
        class="dashboard-btn hover:bg-purple-50"
      >
        <Wallet class="icon text-purple-500" />
        <span>Wallet Vouchers</span>
      </button>

      <!-- Users -->
      <button
        @click="goTo('/admin/users')"
        class="dashboard-btn hover:bg-red-50"
      >
        <User class="icon text-red-500" />
        <span>Users</span>
      </button>

      <!-- Verification -->
      <button
        @click="goTo('/admin/verifications')"
        class="dashboard-btn hover:bg-indigo-50"
      >
        <ShieldCheck class="icon text-indigo-500" />
        <span>Verification</span>
      </button>

      <!-- Settings -->
      <button
        @click="goTo('/admin/settings')"
        class="dashboard-btn hover:bg-gray-100"
      >
        <Settings class="icon text-gray-600" />
        <span>Settings</span>
      </button>

    </main>

  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ShoppingCart, Bolt, Ticket, Wallet, User, Settings, ShieldCheck } from 'lucide-vue-next'

const adminName = ref('Admin')

const goTo = (url) => {
  router.visit(url)
}
</script>

<style scoped>
body {
  direction: rtl;
}

/* تصميم موحد للأزرار */
.dashboard-btn {
  background: white;
  border-radius: 18px;
  padding: 18px 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  font-weight: 600;
  font-size: 13px;
  color: #374151;
  box-shadow: 0 6px 14px rgba(0,0,0,0.05);
  transition: all 0.25s ease;
}

.dashboard-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 20px rgba(0,0,0,0.08);
}

.icon {
  width: 28px;
  height: 28px;
}

/* تكبير بسيط للشاشات الكبيرة */
@media (min-width: 768px) {
  .dashboard-btn {
    font-size: 14px;
    padding: 20px 14px;
  }

  .icon {
    width: 32px;
    height: 32px;
  }
}
</style>
