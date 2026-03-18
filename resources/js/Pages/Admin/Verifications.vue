
<template>
  <AdminLayout>
    <div class="min-h-screen p-6 bg-gray-100">

      <h1 class="text-2xl font-bold mb-6">User Verifications</h1>

      <!-- Flash message -->
      <div v-if="flashMessage" class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ flashMessage }}
      </div>

      <!-- Filters -->
      <div class="mb-4 flex space-x-2">
        <button @click="filter='all'" :class="buttonClass('all')">All</button>
        <button @click="filter='unverified'" :class="buttonClass('unverified')">Unverified</button>
        <button @click="filter='pending'" :class="buttonClass('pending')">Pending</button>
        <button @click="filter='rejected'" :class="buttonClass('rejected')">Rejected</button>
        <button @click="filter='verified'" :class="buttonClass('verified')">Verified</button>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">

          <thead class="bg-gray-200">
            <tr>
              <th class="px-4 py-2 text-left text-sm">#ID</th>
              <th class="px-4 py-2 text-left text-sm">Name</th>
              <th class="px-4 py-2 text-left text-sm">Email</th>
              <th class="px-4 py-2 text-left text-sm">Phone</th>
              <th class="px-4 py-2 text-left text-sm">National ID</th>
              <th class="px-4 py-2 text-left text-sm">ID Card</th>
              <th class="px-4 py-2 text-left text-sm">Selfie</th>
              <th class="px-4 py-2 text-left text-sm">Status</th>
              <th class="px-4 py-2 text-left text-sm">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="user in filteredUsers" :key="user.id" class="border-b hover:bg-gray-50">

              <td class="px-4 py-2">{{ user.id }}</td>
              <td class="px-4 py-2">{{ user.name }}</td>
              <td class="px-4 py-2">{{ user.email }}</td>
              <td class="px-4 py-2">{{ user.phone || '-' }}</td>
              <td class="px-4 py-2">{{ user.national_id || '-' }}</td>

              <!-- ID CARD -->
              <td class="px-4 py-2">
                <a
                  v-if="user.id_card_image"
                  :href="'/storage/' + user.id_card_image"
                  target="_blank"
                  class="text-blue-600 underline"
                >
                  View
                </a>
                <span v-else>-</span>
              </td>

              <!-- SELFIE -->
              <td class="px-4 py-2">
                <a
                  v-if="user.selfie_with_id_image"
                  :href="'/storage/' + user.selfie_with_id_image"
                  target="_blank"
                  class="text-blue-600 underline"
                >
                  View
                </a>
                <span v-else>-</span>
              </td>

              <!-- STATUS -->
              <td class="px-4 py-2">

                <span
                  v-if="user.verification_status==='verified'"
                  class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs"
                >
                  Verified
                </span>

                <span
                  v-else-if="user.verification_status==='pending'"
                  class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs"
                >
                  Pending
                </span>

                <span
                  v-else-if="user.verification_status==='unverified'"
                  class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs"
                >
                  Unverified
                </span>

                <span
                  v-else
                  class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs"
                >
                  Rejected
                </span>

              </td>

              <!-- ACTIONS -->
              <td class="px-4 py-2 space-x-2">

                <button
                  v-if="['pending','unverified'].includes(user.verification_status)"
                  @click="updateStatus(user.id,'verified')"
                  class="bg-green-600 text-white px-2 py-1 rounded text-xs"
                >
                  Verify
                </button>

                <button
                  v-if="['pending','unverified'].includes(user.verification_status)"
                  @click="updateStatus(user.id,'rejected')"
                  class="bg-red-600 text-white px-2 py-1 rounded text-xs"
                >
                  Reject
                </button>

              </td>

            </tr>

            <tr v-if="filteredUsers.length===0">
              <td colspan="9" class="text-center py-4 text-gray-500">
                No users found
              </td>
            </tr>

          </tbody>

        </table>
      </div>

    </div>
  </AdminLayout>
</template>


<script setup>

import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  users: Array
})

const page = usePage()
const flashMessage = ref(page.props.flash?.message || '')

const filter = ref('all')

/* FILTER USERS */

const filteredUsers = computed(() => {

  if(filter.value === 'all') return props.users

  return props.users.filter(user =>
    user.verification_status === filter.value
  )

})


/* BUTTON STYLE */

const buttonClass = (btn) => {

  if(filter.value === btn){
    return 'px-3 py-1 rounded text-xs font-semibold bg-blue-600 text-white'
  }

  return 'px-3 py-1 rounded text-xs font-semibold bg-gray-200 text-gray-700 hover:bg-gray-300'

}


/* UPDATE STATUS */

const updateStatus = (userId, status) => {

  const formData = new FormData()

  formData.append('_method','PATCH')
  formData.append('verification_status',status)

  router.post('/admin/verifications/' + userId, formData, {

    onSuccess: () => {

      alert('Status updated')
      location.reload()

    },

    onError: (errors) => {

      alert('Error: ' + JSON.stringify(errors))

    }

  })

}

</script>


<style scoped>

table th,
table td{
  white-space:nowrap;
}

</style>