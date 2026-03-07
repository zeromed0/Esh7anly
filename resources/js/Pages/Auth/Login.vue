
<script setup>
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps({
    canResetPassword: Boolean,
    status: String,
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>

<Head title="Login" />

<div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-blue-600 to-blue-800 px-4">

    <!-- Logo Area -->
    <div class="mb-8 text-center animate-fade-down">
        <!-- ضع صورة الشعار هنا -->
        <img src="/images/logo1.png" class="h-20 mx-auto mb-5" />
        <p class="text-blue-100 text-sm">
            شحن الألعاب بسهولة
        </p>
    </div>


    <!-- Login Card -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 animate-fade-up">

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">

            <!-- Email -->
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                />

                <InputError class="mt-2" :message="form.errors.email" />
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
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>


            <!-- Remember -->
            <div class="mt-4 flex items-center justify-between">

                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">
                        Remember me
                    </span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-blue-600 hover:underline"
                >
                    Forgot password?
                </Link>

            </div>


            <!-- Button -->
            <PrimaryButton
                class="mt-6 w-full justify-center bg-blue-600 hover:bg-blue-700 transition"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Log in
            </PrimaryButton>

        </form>

    </div>

</div>

</template>

<style>

@keyframes fadeUp {
from {
opacity: 0;
transform: translateY(20px);
}
to {
opacity: 1;
transform: translateY(0);
}
}

@keyframes fadeDown {
from {
opacity: 0;
transform: translateY(-20px);
}
to {
opacity: 1;
transform: translateY(0);
}
}

.animate-fade-up{
animation: fadeUp .6s ease;
}

.animate-fade-down{
animation: fadeDown .6s ease;
}

</style>