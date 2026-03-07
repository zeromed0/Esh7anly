
<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    users: Object,
    stats: Object,
    taxPercent: Number
})

const search = ref('')
const filterBanned = ref('all')
const filterPremium = ref('all')
const balanceSort = ref('none')

const activityUser = ref(null)
const activityLogs = ref([])
const adminNote = ref('')
const noteUserId = ref(null)

const filteredUsers = computed(() => {
    let data = props.users.data || []

    if (search.value) {
        const term = search.value.toLowerCase()
        data = data.filter(u =>
            (u.name && u.name.toLowerCase().includes(term)) ||
            (u.email && u.email.toLowerCase().includes(term)) ||
            String(u.id).includes(term)
        )
    }

    if (filterBanned.value === 'banned') data = data.filter(u => u.is_banned)
    else if (filterBanned.value === 'active') data = data.filter(u => !u.is_banned)

    if (filterPremium.value === 'premium') data = data.filter(u => u.is_premium)
    else if (filterPremium.value === 'normal') data = data.filter(u => !u.is_premium)

    if (balanceSort.value === 'asc') data = data.slice().sort((a,b) => a.balance - b.balance)
    else if (balanceSort.value === 'desc') data = data.slice().sort((a,b) => b.balance - a.balance)

    return data
})

const toggleBan = (user) => {
    if (!confirm("هل أنت متأكد من " + (user.is_banned ? "إلغاء الحظر" : "حظر") + " المستخدم؟")) return
    router.post('/admin/user/' + user.id + '/toggle-ban', {}, { preserveScroll: true })
}


const togglePremium = (user) => {

    // إذا كان لديه Premium → إلغاء مباشر
    if (user.is_premium) {
        if (!confirm("هل أنت متأكد من إلغاء Premium للمستخدم؟")) return

        router.post('/admin/user/' + user.id + '/toggle-premium', {
            days: 0
        }, { preserveScroll: true })

        return
    }

    // إذا لم يكن لديه Premium → اطلب عدد الأيام
    const days = prompt("كم عدد الأيام التي تريد منحها للمستخدم؟")

    if (!days) return

    if (isNaN(days) || Number(days) <= 0) {
        alert("يرجى إدخال رقم صحيح أكبر من صفر")
        return
    }

    if (!confirm("هل أنت متأكد من منح " + days + " يوم Premium؟")) return

    router.post('/admin/user/' + user.id + '/toggle-premium', {
        days: Number(days)
    }, { preserveScroll: true })
}

const setNoteUser = (user) => {
    noteUserId.value = user.id
    adminNote.value = user.admin_note || ''
}

const updateNote = (user) => {
    if (!confirm("هل أنت متأكد من تحديث الملاحظة؟")) return

    router.post('/admin/user/' + user.id + '/update-note', {
        admin_note: adminNote.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            user.admin_note = adminNote.value
            noteUserId.value = null
        }
    })
}

const openActivityModal = (user) => {
    router.get('/admin/user/' + user.id + '/activity', {}, {
        preserveScroll: true,
        onSuccess: (page) => {
            activityLogs.value = page.props.activity || []
            activityUser.value = user
        }
    })
}

const premiumDaysLeft = (user) => {
    if (!user.premium_until) return 0
    const now = new Date()
    const until = new Date(user.premium_until)
    const diff = until - now
    return diff > 0 ? Math.ceil(diff / (1000*60*60*24)) : 0
}

const goTo = (url) => {
    if (url) router.visit(url)
}
</script>

<template>
  <AdminLayout>
   <div class="p-6 space-y-6">

    <h1 class="text-2xl font-bold mb-4">إدارة المستخدمين</h1>

    
<!-- الإحصائيات -->
<div class="grid grid-cols-4 gap-4 mb-4">
    <div class="bg-white shadow p-4 rounded">
        <div class="text-gray-500">المستخدمين</div>
        <div class="text-xl font-bold">{{ stats.totalUsers }}</div>
    </div>
    <div class="bg-white shadow p-4 rounded">
        <div class="text-gray-500">إجمالي الرصيد</div>
        <div class="text-xl font-bold">{{ stats.totalBalance }}</div>
    </div>
    <div class="bg-white shadow p-4 rounded">
        <div class="text-gray-500">الطلبات المكتملة</div>
        <div class="text-xl font-bold">{{ stats.totalCompletedOrders }}</div>
    </div>
    <div class="bg-white shadow p-4 rounded">
        <div class="text-gray-500">إجمالي الإنفاق</div>
        <div class="text-xl font-bold">{{ stats.totalRevenue }}</div>
    </div>
</div>

    <!-- البحث والفلترة -->
    <div class="flex flex-wrap gap-2 mb-4">
        <input v-model="search" type="text" placeholder="بحث بالاسم أو الإيميل أو ID"
               class="border px-3 py-2 rounded w-1/3"/>
        <select v-model="filterBanned" class="border px-3 py-2 rounded">
            <option value="all">الكل</option>
            <option value="banned">محظور</option>
            <option value="active">نشط</option>
        </select>
        <select v-model="filterPremium" class="border px-3 py-2 rounded">
            <option value="all">الكل</option>
            <option value="premium">مميز</option>
            <option value="normal">عادي</option>
        </select>
        <select v-model="balanceSort" class="border px-3 py-2 rounded">
            <option value="none">ترتيب الرصيد</option>
            <option value="desc">أعلى → أقل</option>
            <option value="asc">أقل → أعلى</option>
        </select>
    </div>

    <!-- جدول -->
    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="w-full text-sm text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">ID</th>
                    <th class="p-3">الاسم</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">عدد الطلبات</th>
                    <th class="p-3">مجموع الإنفاق</th>
                    <th class="p-3">الرصيد</th>
                    <th class="p-3">Premium حتى</th>
                    <th class="p-3">تاريخ التسجيل</th>
                    <th class="p-3">ملاحظة الأدمن</th>
                    <th class="p-3">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in filteredUsers" :key="user.id" class="border-t hover:bg-gray-50">
                    <td class="p-3">{{ user.id }}</td>
                    <td class="p-3 font-semibold">{{ user.name }}</td>
                    <td class="p-3">{{ user.email }}</td>
                    <td class="p-3">{{ user.orders_count || 0 }}</td>
                    <td class="p-3 text-green-600 font-semibold">{{ user.total_spent || 0 }}</td>
                    <td class="p-3">{{ user.balance }}</td>
                    <td class="p-3">
                        <span v-if="user.premium_until" class="bg-purple-600 text-white px-2 py-1 rounded text-xs">
                            {{ new Date(user.premium_until).toLocaleDateString() }}
                        </span>
                        <span v-else class="text-gray-400">-</span>
                    </td>
                    <td class="p-3">
                        {{ new Date(user.created_at).toLocaleDateString() }}
                    </td>
                    <td class="p-3">
                        <div v-if="user.admin_note" class="text-xs bg-gray-100 p-2 rounded mb-1">
                            {{ user.admin_note }}
                        </div>

                        <input v-model="adminNote"
                               @focus="setNoteUser(user)"
                               type="text"
                               placeholder="ملاحظة"
                               class="border px-2 py-1 rounded text-xs w-full"/>

                        <button @click="updateNote(user)"
                                class="mt-1 bg-gray-600 text-white px-2 py-1 rounded text-xs w-full">
                            تحديث
                        </button>
                    </td>
                    <td class="p-3 flex flex-wrap gap-1 justify-center">
                        <button @click="toggleBan(user)" class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                            {{ user.is_banned ? 'إلغاء الحظر' : 'حظر' }}
                        </button>
                        <button @click="togglePremium(user)" class="bg-purple-600 text-white px-2 py-1 rounded text-xs">
                            {{ user.is_premium ? 'إلغاء Premium' : 'منح Premium' }}
                        </button>
                        <button @click="openActivityModal(user)" class="bg-gray-700 text-white px-2 py-1 rounded text-xs">
                            معاملات
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between mt-4">
        <button @click="goTo(props.users.prev_page_url)" :disabled="!props.users.prev_page_url"
                class="px-3 py-1 border rounded">السابق</button>
        <button @click="goTo(props.users.next_page_url)" :disabled="!props.users.next_page_url"
                class="px-3 py-1 border rounded">التالي</button>
    </div>

    <!-- Activity Modal -->
    <div v-if="activityUser" class="fixed inset-0 bg-black/40 flex items-center justify-center">
        <div class="bg-white p-6 rounded w-2/3 max-h-[70vh] overflow-y-auto">
            <h2 class="font-bold mb-4">معاملات - {{ activityUser.name }}</h2>
            <table class="w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2">النوع</th>
                        <th class="p-2">المبلغ</th>
                        <th class="p-2">الرصيد بعد</th>
                        <th class="p-2">التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in activityLogs" :key="log.id" class="border-b">
                        <td class="p-2">{{ log.type }}</td>
                        <td class="p-2">{{ log.amount }}</td>
                        <td class="p-2">{{ log.balance_after }}</td>
                        <td class="p-2">{{ new Date(log.created_at).toLocaleString() }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="flex justify-end mt-4">
                <button @click="activityUser=null" class="border px-3 py-1 rounded">إغلاق</button>
            </div>
        </div>
    </div>
   </div>
  </AdminLayout>
</template>