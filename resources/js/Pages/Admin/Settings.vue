
<script setup>
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    settings: Object
})

const form = useForm({
    ...props.settings
})

const submit = () => {
    form.post('/admin/settings')
}
</script>

<template>
    <AdminLayout>
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">⚙️ إعدادات النظام</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div>
            <label>نسبة الضريبة (%)</label>
            <input type="number" v-model="form.tax_percent" class="input" />
        </div>

        <div>
            <label>تفعيل الضريبة</label>
            <select v-model="form.tax_enabled" class="input">
                <option :value="true">مفعلة</option>
                <option :value="false">معطلة</option>
            </select>
        </div>

        <div>
            <label>إعفاء البريميوم من الضريبة</label>
            <select v-model="form.premium_exempt_tax" class="input">
                <option :value="true">نعم</option>
                <option :value="false">لا</option>
            </select>
        </div>

        <div>
            <label>خصم البريميوم (%)</label>
            <input type="number" v-model="form.premium_discount_percent" class="input" />
        </div>

        <div>
            <label>حد الإنفاق اليومي (MRU)</label>
            <input type="number" v-model="form.daily_spend_limit" class="input" />
        </div>

        <div>
            <label>بان تلقائي عند تجاوز الحد</label>
            <select v-model="form.auto_ban_on_limit" class="input">
                <option :value="true">مفعل</option>
                <option :value="false">معطل</option>
            </select>
        </div>

        <div>
            <label>تفعيل الموقع</label>
            <select v-model="form.site_enabled" class="input">
                <option :value="true">مفعل</option>
                <option :value="false">صيانة</option>
            </select>
        </div>

        <div>
            <label>رسالة الصيانة</label>
            <input type="text" v-model="form.maintenance_message" class="input" />
        </div>

        <div>
            <label>أقل مبلغ للطلب</label>
            <input type="number" v-model="form.min_order_amount" class="input" />
        </div>

        <div>
            <label>أعلى مبلغ للطلب</label>
            <input type="number" v-model="form.max_order_amount" class="input" />
        </div>

        <div>
            <label>مكافأة التسجيل</label>
            <input type="number" v-model="form.register_bonus" class="input" />
        </div>

        <div>
            <label>تفعيل Topup</label>
            <select v-model="form.topup_enabled" class="input">
                <option :value="true">مفعل</option>
                <option :value="false">معطل</option>
            </select>
        </div>

        <div>
            <label>تفعيل Voucher</label>
            <select v-model="form.voucher_enabled" class="input">
                <option :value="true">مفعل</option>
                <option :value="false">معطل</option>
            </select>
        </div>

    </div>

    <button @click="submit"
        class="mt-6 bg-blue-600 text-white px-6 py-2 rounded">
        حفظ الإعدادات
    </button>

    <div v-if="form.errors" class="text-red-500 mt-4">
        {{ form.errors }}
    </div>
 </div>
  </AdminLayout>
</template>

<style>
.input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 6px;
}
</style>