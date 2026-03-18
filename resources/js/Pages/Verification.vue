
<template>
  <UserLayout :user="user">
    <div class="min-h-screen flex justify-center p-6 bg-gradient-to-br from-blue-50 via-white to-blue-100">

      <div class="w-full max-w-2xl space-y-6">

        <!-- Header -->
        <div class="text-center mb-6">
          <h1 class="text-2xl font-bold text-gray-800">Account Verification</h1>
          <p class="text-gray-500 text-sm mt-1">
            Please upload your documents to verify your account
          </p>
        </div>

        <!-- Current Verification Status -->
        <div class="bg-white rounded-xl shadow p-4 text-center">
          <p class="text-sm">
            Status: 
            <span v-if="user.verification_status === 'verified'" class="text-green-600 font-semibold">Verified ✅</span>
            <span v-else-if="user.verification_status === 'pending'" class="text-yellow-600 font-semibold">Pending ⏳</span>
            <span v-else-if="user.verification_status === 'rejected'" class="text-red-600 font-semibold">Rejected ❌</span>
            <span v-else class="text-gray-600 font-semibold">Not Verified ⚠️</span>
          </p>
          <p v-if="user.admin_note" class="mt-2 text-xs text-red-500">
            Admin Note: {{ user.admin_note }}
          </p>
        </div>

        <!-- Upload Form -->
        <div v-if="canUpload" class="bg-white rounded-xl shadow p-6">
          <form @submit.prevent="submitVerification" class="space-y-4">

            <!-- ID Card -->
            <div>
              <label class="block text-gray-700 text-sm font-semibold mb-1">ID Card Image</label>
              <input type="file" @change="onFileChange('id_card_image', $event)" accept="image/*" class="w-full text-sm" />
              <p v-if="previews.id_card_image" class="mt-2 text-xs text-gray-500">Preview:</p>
              <img v-if="previews.id_card_image" :src="previews.id_card_image" class="mt-1 h-24 rounded border" />
            </div>

            <!-- Selfie with ID -->
            <div>
              <label class="block text-gray-700 text-sm font-semibold mb-1">Selfie with ID</label>
              <input type="file" @change="onFileChange('selfie_with_id_image', $event)" accept="image/*" class="w-full text-sm" />
              <p v-if="previews.selfie_with_id_image" class="mt-2 text-xs text-gray-500">Preview:</p>
              <img v-if="previews.selfie_with_id_image" :src="previews.selfie_with_id_image" class="mt-1 h-24 rounded border" />
            </div>

            <!-- Submit -->
            <div class="mt-4">
              <PrimaryButton :disabled="processing" class="w-full justify-center bg-blue-600 hover:bg-blue-700">
                <span v-if="processing">Uploading...</span>
                <span v-else>Submit Verification</span>
              </PrimaryButton>
            </div>

          </form>
        </div>

        <!-- Disabled Message -->
        <div v-else class="text-center p-4 bg-yellow-100 text-yellow-700 rounded">
          You cannot upload new documents until your current verification is reviewed.
        </div>

        <!-- Success / Error Message -->
        <div v-if="message" class="text-center text-sm" :class="messageClass">
          {{ message }}
        </div>

      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
  user: { type: Object, required: true }
})

const previews = ref({
  id_card_image: null,
  selfie_with_id_image: null,
})

const files = ref({
  id_card_image: null,
  selfie_with_id_image: null,
})

const message = ref('')
const messageClass = ref('text-green-600')
const processing = ref(false)

// يمكن رفع الملفات فقط إذا الحالة unverified أو rejected
const canUpload = computed(() => {
  return props.user.verification_status === 'unverified' || props.user.verification_status === 'rejected'
})

// Handle file preview
const onFileChange = (field, event) => {
  const file = event.target.files[0]
  if (!file) return
  files.value[field] = file
  const reader = new FileReader()
  reader.onload = e => previews.value[field] = e.target.result
  reader.readAsDataURL(file)
}

// Submit verification form
const submitVerification = () => {
  if (!files.value.id_card_image || !files.value.selfie_with_id_image) {
    message.value = 'Please upload both images'
    messageClass.value = 'text-red-600'
    return
  }

  const formData = new FormData()
  formData.append('id_card_image', files.value.id_card_image)
  formData.append('selfie_with_id_image', files.value.selfie_with_id_image)

  processing.value = true
  router.post('/user/verification/upload', formData, {
    preserveState: true,
    onSuccess: () => {
      message.value = 'Verification submitted successfully. Status is now Pending.'
      messageClass.value = 'text-green-600'
      processing.value = false
      // منع إعادة الرفع فورًا
      previews.value.id_card_image = null
      previews.value.selfie_with_id_image = null
    },
    onError: (errors) => {
      message.value = errors.message || 'Submission failed'
      messageClass.value = 'text-red-600'
      processing.value = false
    }
  })
}
</script>

<style scoped>
</style>