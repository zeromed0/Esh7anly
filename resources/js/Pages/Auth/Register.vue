
<template>
  <Head title="Register" />

  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-blue-600 to-blue-800 px-4">

    <!-- Logo Area -->
    <div class="mb-8 text-center animate-fade-down">
      <img src="/images/logo1.png" class="h-20 mx-auto mb-5" />
      <p class="text-blue-100 text-sm">
        انضم إلينا وابدأ شحن ألعابك بسهولة
      </p>
    </div>

    <!-- Register Card -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 animate-fade-up">

      <form @submit.prevent="submit">

        <!-- Name -->
        <div>
          <InputLabel for="name" value="Name" />
          <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
            required
            autofocus
            autocomplete="name"
          />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <!-- Email -->
        <div class="mt-4">
          <InputLabel for="email" value="Email" />
          <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full"
            v-model="form.email"
            required
            autocomplete="username"
          />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <!-- Phone (Mauritania only) -->
        <div class="mt-4">
          <InputLabel for="phone" value="Phone (Mauritania)" />
          <TextInput
            id="phone"
            type="tel"
            class="mt-1 block w-full"
            v-model="form.phone"
            required
            placeholder="Ex: 22234567"
          />
          <InputError class="mt-2" :message="form.errors.phone" />
        </div>

        <!-- National ID -->
        <div class="mt-4">
          <InputLabel for="national_id" value="National ID" />
          <TextInput
            id="national_id"
            type="text"
            class="mt-1 block w-full"
            v-model="form.national_id"
            required
            placeholder="12-digit ID"
          />
          <InputError class="mt-2" :message="form.errors.national_id" />
        </div>

        <!-- Password -->
        <div class="mt-4">
          <InputLabel for="password" value="Password" />
          <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password"
            required
            autocomplete="new-password"
          />
          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
          <InputLabel for="password_confirmation" value="Confirm Password" />
          <TextInput
            id="password_confirmation"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password_confirmation"
            required
            autocomplete="new-password"
          />
          <InputError class="mt-2" :message="form.errors.password_confirmation" />
        </div>

        <!-- Actions -->
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between">
          <Link
            :href="route('login')"
            class="text-sm text-blue-600 underline hover:text-blue-700 mb-4 sm:mb-0"
          >
            Already registered?
          </Link>

          <PrimaryButton
            class="w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 transition"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Register
          </PrimaryButton>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup>
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  phone: '',
  national_id: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  // التحقق من رقم الهاتف موريتانيا فقط (8 أرقام يبدأ بـ 2 أو 6)
  const phoneRegex = /^(2|3|4)\d{7}$/
  if (!phoneRegex.test(form.phone)) {
    form.setError('phone', 'Invalid Mauritanian phone number')
    return
  }

  // التحقق من رقم التعريف الوطني (12 رقم)
  const nationalIdRegex = /^\d{10}$/
  if (!nationalIdRegex.test(form.national_id)) {
    form.setError('national_id', 'National ID must be 10 digits')
    return
  }

  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<style>
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeDown {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-up { animation: fadeUp .6s ease; }
.animate-fade-down { animation: fadeDown .6s ease; }
</style>