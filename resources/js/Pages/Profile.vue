<template>
  <UserLayout :user="user">
    <div class="min-h-screen p-5 md:p-10 flex justify-center">
      <div class="w-full max-w-2xl space-y-6">

        <!-- بطاقة البروفايل -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-center gap-4">
            <img
              :src="user.avatar ? '/storage/' + user.avatar : '/images/default-avatar.png'"
              class="w-20 h-20 rounded-full border-4 border-blue-500 shadow"
            />

            <div class="flex-1">
              <div class="flex items-center justify-between">
                <div>
                  <h2 class="text-lg font-semibold">{{ user.name }}</h2>
                  <p class="text-sm text-gray-500">{{ user.email }}</p>
                  <p class="text-xs text-gray-400 mt-1">User ID: {{ user.id }}</p>
                  <p class="text-xs text-gray-400 mt-1">Joined: {{ formatDate(user.created_at) }}</p>
                </div>

                <div class="text-right">
                  <button @click="triggerAvatar" class="text-sm px-3 py-1 border rounded">Change photo</button>
                  <input ref="avatarInput" type="file" class="hidden" @change="uploadAvatar" />
                </div>
              </div>

              <div class="mt-4 flex gap-3">
                <form @submit.prevent="submitName" class="flex gap-2 items-center">
                  <input v-model="form.name" class="border rounded px-3 py-2 text-sm" />
                  <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded text-sm">Save</button>
                </form>

                <button @click="openPassword = !openPassword" class="text-sm px-3 py-2 border rounded">Change password</button>
              </div>
            </div>
          </div>

          <!-- change password -->
          <div v-if="openPassword" class="mt-4 border-t pt-4">
            <form @submit.prevent="submitPassword" class="space-y-3">
              <div>
                <label class="text-xs text-gray-600">Current password</label>
                <input v-model="pw.current_password" type="password" class="w-full border rounded px-3 py-2 text-sm" />
              </div>

              <div>
                <label class="text-xs text-gray-600">New password</label>
                <input v-model="pw.password" type="password" class="w-full border rounded px-3 py-2 text-sm" />
              </div>

              <div>
                <label class="text-xs text-gray-600">Confirm new</label>
                <input v-model="pw.password_confirmation" type="password" class="w-full border rounded px-3 py-2 text-sm" />
              </div>

              <div class="flex gap-2">
                <button class="bg-green-600 text-white px-3 py-2 rounded text-sm">Update password</button>
                <button type="button" @click="resetPasswordForm" class="text-sm px-3 py-2 border rounded">Cancel</button>
              </div>
            </form>
          </div>
        </div>

        <!-- التوثيق -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="font-semibold">Verification</h3>
              <p class="text-sm text-gray-600 mt-1">
                Status:
                <span v-if="user.identity_status === 'pending'" class="text-yellow-600">Pending review</span>
                <span v-else-if="user.identity_status === 'approved'" class="text-green-600">Approved</span>
                <span v-else-if="user.identity_status === 'rejected'" class="text-red-600">Rejected</span>
                <span v-else class="text-gray-600">Not uploaded</span>
              </p>
            </div>

            <div class="text-right">
              <p class="text-sm text-gray-500">Daily limit: <strong>{{ dailyLimit }}</strong></p>
              <p class="text-xs text-gray-400 mt-1">Used today: {{ usedToday }}</p>
            </div>
          </div>

          <div class="mt-4">
            <div class="mb-3">
              <label class="text-sm">Identity card (front)</label>
              <input ref="idInput" type="file" @change="onSelectIdentity" class="w-full mt-2" />
            </div>

            <div class="mb-3">
              <label class="text-sm">Selfie holding ID + phone</label>
              <input ref="selfieInput" type="file" @change="onSelectSelfie" class="w-full mt-2" />
            </div>

            <!-- الزر يظهر دائماً -->
            <div class="flex gap-2 items-center mt-3">
              <button @click="uploadVerification" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
                Upload Documents
              </button>

              <button v-if="user.identity_card" @click="viewFile('identity')" class="text-sm px-3 py-1 border rounded">View identity</button>
              <button v-if="user.selfie" @click="viewFile('selfie')" class="text-sm px-3 py-1 border rounded">View selfie</button>
            </div>

            <p v-if="message" :class="messageClass" class="mt-3 text-sm">{{ message }}</p>
          </div>
        </div>

        <!-- الإحصائيات -->
        <div class="grid grid-cols-3 gap-4">
          <div class="bg-white rounded-2xl shadow p-4 text-center">
            <p class="text-xs text-gray-500">Wallet</p>
            <p class="text-lg font-bold text-blue-600">{{ balance }} MRU</p>
          </div>

          <div class="bg-white rounded-2xl shadow p-4 text-center">
            <p class="text-xs text-gray-500">Transactions</p>
            <p class="text-lg font-bold text-blue-600">{{ transactionsCount }}</p>
          </div>

          <div class="bg-white rounded-2xl shadow p-4 text-center">
            <p class="text-xs text-gray-500">Joined</p>
            <p class="text-lg font-bold text-blue-600">{{ formatDate(user.created_at) }}</p>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'

const props = defineProps({
  user: Object,
  balance: Number,
  transactionsCount: Number,
  usedToday: Number,
  dailyLimit: Number
})

// reactive form for name
const form = ref({
  name: props.user.name || ''
})

// password form
const openPassword = ref(false)
const pw = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

// avatar input ref
const avatarInput = ref(null)
const triggerAvatar = () => avatarInput.value.click()

// identity / selfie files
const idFile = ref(null)
const selfieFile = ref(null)
const idInput = ref(null)
const selfieInput = ref(null)

// messages
const message = ref('')
const messageClass = ref('text-green-600')

// helpers
const balance = props.balance
const transactionsCount = props.transactionsCount
const usedToday = props.usedToday
const dailyLimit = props.dailyLimit

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric', month: 'long', day: 'numeric'
  })
}

// select files handlers
const onSelectIdentity = (e) => idFile.value = e.target.files[0] || null
const onSelectSelfie = (e) => selfieFile.value = e.target.files[0] || null

// view existing uploaded files
const viewFile = (type) => {
  if (type === 'identity' && props.user.identity_card) {
    window.open('/storage/' + props.user.identity_card, '_blank')
  }
  if (type === 'selfie' && props.user.selfie) {
    window.open('/storage/' + props.user.selfie, '_blank')
  }
}

// submit name
const submitName = () => {
  router.patch('/user/profile', { name: form.value.name }, {
    onSuccess: () => {
      message.value = 'Name updated'
      messageClass.value = 'text-green-600'
    },
    onError: (errors) => {
      message.value = errors.name ? errors.name[0] : 'Failed'
      messageClass.value = 'text-red-600'
    }
  })
}

// submit password
const submitPassword = () => {
  router.patch('/user/profile/password', { ...pw.value }, {
    onSuccess: () => {
      message.value = 'Password changed'
      messageClass.value = 'text-green-600'
      pw.value.current_password = ''
      pw.value.password = ''
      pw.value.password_confirmation = ''
      openPassword.value = false
    },
    onError: (errors) => {
      message.value = errors.password ? errors.password[0] : (errors.current_password ? errors.current_password[0] : 'Failed')
      messageClass.value = 'text-red-600'
    }
  })
}

const resetPasswordForm = () => {
  pw.value.current_password = ''
  pw.value.password = ''
  pw.value.password_confirmation = ''
  openPassword.value = false
}

// upload avatar
const uploadAvatar = (e) => {
  const file = e.target.files[0]
  if (!file) return
  const fd = new FormData()
  fd.append('avatar', file)
  router.post('/user/profile/avatar', fd, {
    onSuccess: () => {
      message.value = 'Avatar updated'
      messageClass.value = 'text-green-600'
    },
    onError: () => {
      message.value = 'Avatar upload failed'
      messageClass.value = 'text-red-600'
    }
  })
}

// upload verification (identity/selfie) — button always visible
const uploadVerification = () => {
  const fd = new FormData()
  if (idFile.value) fd.append('identity_card', idFile.value)
  if (selfieFile.value) fd.append('selfie', selfieFile.value)

  if (!idFile.value && !selfieFile.value) {
    message.value = 'Please choose at least one file to upload'
    messageClass.value = 'text-red-600'
    return
  }

  router.post('/user/profile/identity', fd, {
    onSuccess: () => {
      message.value = 'Documents uploaded — awaiting review'
      messageClass.value = 'text-green-600'
      // clear inputs
      if (idInput.value) idInput.value.value = ''
      if (selfieInput.value) selfieInput.value.value = ''
      idFile.value = null
      selfieFile.value = null
    },
    onError: (errors) => {
      const msg = errors.identity ? errors.identity[0] : (errors.selfie ? errors.selfie[0] : 'Upload failed')
      message.value = msg
      messageClass.value = 'text-red-600'
    }
  })
}
</script>

<style scoped>
/* minimal local styles to avoid Tailwind @apply issues */
</style>