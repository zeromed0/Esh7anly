
<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="mb-4 text-sm text-gray-600">
            شكراً لتسجيلك! قبل المتابعة، يرجى التحقق من بريدك الإلكتروني بالنقر على الرابط الذي أرسلناه إليك. إذا لم تستلم البريد، سنرسل لك آخر جديد.
        </div>

        <div v-if="verificationLinkSent" class="mb-4 text-sm font-medium text-green-600">
            تم إرسال رابط التحقق الجديد إلى بريدك الإلكتروني.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    إعادة إرسال بريد التحقق
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    تسجيل الخروج
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>