<template>
  <AdminLayout>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">بطاقات الشحن (Vouchers)</h1>

    <!-- ======== إدارة الألعاب ======== -->
    <section class="mb-10">
      <button @click="showGameForm = !showGameForm"
        class="bg-blue-600 text-white px-4 py-2 rounded mb-4 hover:bg-blue-700 transition">
        {{ showGameForm ? 'إلغاء' : 'إضافة لعبة جديدة' }}
      </button>

      <!-- نموذج إضافة / تعديل لعبة -->
      <div v-if="showGameForm || gameEditing.id" class="p-4 bg-white rounded shadow mb-6">
        <input v-model="gameForm.name" placeholder="اسم اللعبة" class="border p-2 rounded w-full mb-2" />
        <input v-model="gameForm.description" placeholder="الوصف (اختياري)" class="border p-2 rounded w-full mb-2" />
        <input v-model="gameForm.image" placeholder="رابط الصورة (اختياري)" class="border p-2 rounded w-full mb-2" />
        <div class="flex gap-2">
          <button @click="gameEditing.id ? updateGame() : storeGame()"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
            {{ gameEditing.id ? 'تحديث اللعبة' : 'حفظ اللعبة' }}
          </button>
          <button v-if="gameEditing.id" @click="cancelEditGame"
            class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">إلغاء</button>
        </div>
      </div>

      <!-- قائمة الألعاب -->
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
  <div v-for="game in games" :key="game.id"
    class="bg-white rounded shadow hover:shadow-lg transition cursor-pointer relative flex flex-col items-center p-4"
    @click="selectGame(game)">
    
    <img :src="game.image || '/images/default-game.png'" class="w-24 h-24 object-cover rounded mb-2" />
    <h2 class="font-semibold text-center">{{ game.name }}</h2>
    <p class="text-sm text-gray-500 text-center">{{ game.description }}</p>

    <!-- زر التفعيل / التعطيل -->
    <button @click.stop="toggleGameActive(game)"
      :class="game.is_active ? 'bg-green-600' : 'bg-gray-400'"
      class="absolute top-2 right-2 text-white px-2 py-1 rounded text-xs">
      {{ game.is_active ? 'مفعّل' : 'معطّل' }}
    </button>

    <!-- أزرار التحكم -->
    <div class="flex gap-2 mt-3 flex-wrap justify-center">
      <button @click.stop="editGame(game)"
        class="bg-yellow-400 text-white px-3 py-1 rounded text-sm hover:bg-yellow-500">
        تعديل
      </button>
      <button @click.stop="destroyGame(game.id)"
        class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
        حذف
      </button>
    </div>
  </div>

      </div>
    </section>

    <!-- ======== إدارة العروض ======== -->
    <section v-if="selectedGame.id" class="mb-10">
      <h3 class="text-xl font-semibold mb-4 text-gray-800">العروض - {{ selectedGame.name }}</h3>

      <!-- العروض -->
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        <div v-for="offer in selectedGame.offers" :key="offer.id"
          class="bg-white rounded shadow hover:shadow-lg transition relative flex flex-col items-center p-4">

          <img :src="offer.image || '/images/default-offer.png'" class="w-24 h-24 object-cover rounded mb-2" />
          <h4 class="font-semibold">{{ offer.name }}</h4>
          <p class="text-gray-600 text-sm">{{ offer.price }} د.أ</p>

          <div class="flex gap-2 mt-2 flex-wrap justify-center">
            <button @click.stop="toggleOfferActive(offer)"
              :class="offer.is_active ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 hover:bg-gray-500'"
              class="text-white px-3 py-1 rounded text-sm">
              {{ offer.is_active ? 'مفعّل' : 'معطّل' }}
            </button>
            <button @click.stop="editOffer(offer)"
              class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 text-sm">تعديل</button>
            <button @click.stop="destroyOffer(offer.id)"
              class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">حذف</button>
            <button @click.stop="selectOffer(offer)"
              class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">الأكواد</button>
          </div>

          <!-- ======== إدارة الأكواد ======== -->
          <div v-if="selectedOffer.id === offer.id" class="w-full mt-4 bg-gray-50 p-3 rounded border">
            <h5 class="font-semibold mb-2 text-gray-800 text-center">أكواد العرض</h5>

            <div class="flex gap-2 mb-2">
              <input v-model="newCode" placeholder="أدخل كود جديد" class="border p-2 rounded w-full" />
              <button @click="addCode()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">إضافة</button>
            </div>

            <ul v-if="selectedOffer.voucherCodes && selectedOffer.voucherCodes.length" class="max-h-40 overflow-y-auto">
              <li v-for="(code, i) in selectedOffer.voucherCodes" :key="code.id"
                class="flex justify-between items-center mb-1 bg-white p-2 rounded border">
                <span class="text-gray-700">{{ code.code }}</span>
                <button @click="removeCode(code.id, i)"
                  class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 text-sm">حذف</button>
              </li>
            </ul>

            <p v-else class="text-sm text-gray-500 text-center mt-2">لا توجد أكواد بعد.</p>

            <button @click="deselectOffer()" class="mt-3 bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
              إغلاق
            </button>
          </div>
        </div>
      </div>

      <!-- إضافة / تعديل عرض -->
      <div class="mt-6 p-4 border rounded bg-gray-50">
        <input v-model="offerForm.name" placeholder="اسم العرض" class="border p-2 rounded w-full mb-2" />
        <input v-model="offerForm.price" type="number" placeholder="السعر" class="border p-2 rounded w-full mb-2" />
        <input v-model="offerForm.image" placeholder="رابط الصورة (اختياري)" class="border p-2 rounded w-full mb-2" />
        <div class="flex gap-2">
          <button @click="offerEditing.id ? updateOffer() : storeOffer()"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
            {{ offerEditing.id ? 'تحديث العرض' : 'حفظ العرض' }}
          </button>
          <button v-if="offerEditing.id" @click="cancelEditOffer"
            class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">إلغاء</button>
        </div>
      </div>
    </section>
  </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ games: Array })

// === الألعاب ===
const showGameForm = ref(false)
const gameEditing = ref({})
const selectedGame = ref({})
const gameForm = ref({ name: '', description: '', image: '' })

const storeGame = () => {
  const form = useForm(gameForm.value)
  form.post('/admin/vouchers', {
    onSuccess: () => { gameForm.value = { name: '', description: '', image: '' }; showGameForm.value = false }
  })
}
const updateGame = () => {
  const form = useForm(gameForm.value)
  form.put('/admin/vouchers/game/' + gameEditing.value.id, {
    onSuccess: () => { gameEditing.value = {}; gameForm.value = { name: '', description: '', image: '' } }
  })
}
const cancelEditGame = () => { gameEditing.value = {}; gameForm.value = { name: '', description: '', image: '' } }
const selectGame = (game) => { selectedGame.value = selectedGame.value.id === game.id ? {} : game }
const toggleGameActive = (game) => {
  const form = useForm({})
  form.post('/admin/vouchers/game/' + game.id + '/toggle-active', {
    onSuccess: () => { game.is_active = !game.is_active }
  })
}

// === العروض ===
const offerEditing = ref({})
const offerForm = ref({ game_id: '', name: '', price: '', image: '' })
const selectedOffer = ref({})
const newCode = ref('')

const storeOffer = () => {
  if (!selectedGame.value.id) return
  offerForm.value.game_id = selectedGame.value.id
  const form = useForm(offerForm.value)
  form.post('/admin/vouchers/offer', {
    onSuccess: () => { offerForm.value = { game_id: '', name: '', price: '', image: '' } }
  })
}
const updateOffer = () => {
  const form = useForm(offerForm.value)
  form.put('/admin/vouchers/offer/' + offerEditing.value.id, {
    onSuccess: () => { offerEditing.value = {}; offerForm.value = { game_id: '', name: '', price: '', image: '' } }
  })
}
const editOffer = (offer) => { offerEditing.value = { ...offer }; offerForm.value = { ...offer } }
const cancelEditOffer = () => { offerEditing.value = {}; offerForm.value = { game_id: '', name: '', price: '', image: '' } }
const destroyOffer = (id) => {
  if (confirm('هل تريد حذف هذا العرض؟')) {
    const form = useForm({})
    form.delete('/admin/vouchers/offer/' + id)
  }
}
const toggleOfferActive = (offer) => {
  const form = useForm({})
  form.post('/admin/vouchers/offer/' + offer.id + '/toggle-active', {
    onSuccess: () => { offer.is_active = !offer.is_active }
  })
}

// === الأكواد ===
const selectOffer = async (offer) => {
  if (selectedOffer.value.id === offer.id) { selectedOffer.value = {}; return }
  selectedOffer.value = { ...offer }

  // تحميل الأكواد إن لم تكن موجودة
  if (!selectedOffer.value.voucherCodes || selectedOffer.value.voucherCodes.length === 0) {
    const res = await fetch('/admin/vouchers/offer/' + offer.id + '/codes')
    if (res.ok) {
      const data = await res.json()
      selectedOffer.value.voucherCodes = data.codes || []
    } else {
      selectedOffer.value.voucherCodes = []
    }
  }
}
const deselectOffer = () => { selectedOffer.value = {}; newCode.value = '' }

const addCode = () => {
  if (!newCode.value || !selectedOffer.value.id) return
  const form = useForm({ code: newCode.value })
  form.post('/admin/vouchers/offer/' + selectedOffer.value.id + '/codes', {
    onSuccess: () => {
      if (!selectedOffer.value.voucherCodes) selectedOffer.value.voucherCodes = []
      selectedOffer.value.voucherCodes.push({ id: Date.now(), code: newCode.value })
      newCode.value = ''
    }
  })
}
const removeCode = (codeId, index) => {
  const form = useForm({})
  form.delete('/admin/vouchers/offer/' + selectedOffer.value.id + '/codes/' + codeId, {
    onSuccess: () => { selectedOffer.value.voucherCodes.splice(index, 1) }
  })
}
const editGame = (game) => {
  gameEditing.value = { ...game }
  gameForm.value = { name: game.name, description: game.description, image: game.image }
  showGameForm.value = true
}

const destroyGame = (id) => {
  if (confirm('هل تريد حذف هذه اللعبة؟')) {
    const form = useForm({})
    form.delete('/admin/vouchers/game/' + id)
  }
}

</script>