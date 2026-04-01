
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
        <Head title="تأكيد البريد الإلكتروني" />

        <div class="mb-4 text-sm text-gray-600">
            شكراً لتسجيلك! قبل المتابعة، يرجى التحقق من بريدك الإلكتروني بالنقر على الرابط الذي أرسلناه إليك. إذا لم تستلم البريد، يمكنك إعادة إرسال الرابط.
        </div>

        <div v-if="verificationLinkSent" class="mb-4 text-sm font-medium text-green-600">
            تم إرسال رابط التحقق الجديد إلى بريدك الإلكتروني.
        </div>

        <form @submit.prevent="submit">
    <PrimaryButton :disabled="form.processing">
        إعادة إرسال بريد التحقق
    </PrimaryButton>
        </form>
    </GuestLayout>
</template>