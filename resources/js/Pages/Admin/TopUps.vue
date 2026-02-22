<template>
  <AdminLayout>
  <div class="p-6">

    <h1 class="text-2xl font-bold mb-6 text-gray-800">الشحنات (Top Ups)</h1>

    <!-- زر إضافة لعبة جديدة -->
    <button @click="showGameForm = !showGameForm"
            class="bg-blue-600 text-white px-4 py-2 rounded mb-6 hover:bg-blue-700 transition">
      {{ showGameForm ? 'إلغاء' : 'إضافة لعبة جديدة' }}
    </button>

    <!-- نموذج إضافة لعبة -->
    <div v-if="showGameForm" class="mb-6 p-4 bg-white rounded shadow flex flex-col gap-2">
      <input v-model="newGame.name" placeholder="اسم اللعبة" class="border p-2 rounded w-full"/>
      <input v-model="newGame.description" placeholder="الوصف (اختياري)" class="border p-2 rounded w-full"/>
      <input v-model="newGame.image" placeholder="رابط الصورة (اختياري)" class="border p-2 rounded w-full"/>
      <button @click="storeGame" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
        حفظ
      </button>
    </div>

    <!-- قائمة الألعاب -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
      <div v-for="game in games" :key="game.id" class="bg-white rounded shadow p-4 flex flex-col items-center">
        <img
          :src="game.image || '/images/default-game.png'"
          alt="Game Image"
          class="w-24 h-24 object-cover rounded mb-2 cursor-pointer"
          @click="selectGame(game)"
        />
        <h2 class="text-center font-semibold">{{ game.name }}</h2>
        <p class="text-gray-500 text-sm text-center">{{ game.description }}</p>
        <div class="flex gap-2 mt-2">
          <button @click="editGame(game)" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">تعديل</button>
          <button @click="destroyGame(game.id)" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">حذف</button>
        </div>

        <!-- نموذج تعديل اللعبة يظهر عند اختيار تعديل -->
        <div v-if="gameEditing.id === game.id" class="mt-2 w-full flex flex-col gap-2">
          <input v-model="gameEditing.name" placeholder="اسم اللعبة" class="border p-2 rounded w-full"/>
          <input v-model="gameEditing.description" placeholder="الوصف" class="border p-2 rounded w-full"/>
          <input v-model="gameEditing.image" placeholder="رابط الصورة" class="border p-2 rounded w-full"/>
          <button @click="updateGame" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">تحديث</button>
          <button @click="cancelEditGame" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">إلغاء</button>
        </div>

        <!-- العروض تظهر فقط عند اختيار اللعبة -->
        <div v-if="selectedGame.id === game.id" class="mt-4 w-full">
          <h3 class="font-semibold mb-2">العروض</h3>
          <table class="w-full text-left border-collapse mb-2">
            <thead>
              <tr class="border-b">
                <th class="px-2 py-1">الاسم</th>
                <th class="px-2 py-1">السعر</th>
                <th class="px-2 py-1">صورة العرض</th>
                <th class="px-2 py-1">أفعال</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="offer in game.offers" :key="offer.id" class="border-b">
                <td class="px-2 py-1">{{ offer.name }}</td>
                <td class="px-2 py-1">{{ offer.price }}</td>
                <td class="px-2 py-1">
                  <img :src="offer.image || '/images/default-offer.png'" class="w-12 h-12 object-cover rounded"/>
                </td>
                <td class="px-2 py-1 flex gap-2">
                  <button @click="editOffer(offer, game.id)" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">تعديل</button>
                  <button @click="destroyOffer(offer.id)" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">حذف</button>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- إضافة عرض جديد -->
          <div class="flex flex-col gap-2">
            <button @click="toggleNewOfferForm(game.id)" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition mb-2">
              {{ newOfferForm[game.id] ? 'إلغاء' : 'إضافة عرض جديد' }}
            </button>
            <div v-if="newOfferForm[game.id]" class="flex flex-col gap-2 p-2 border rounded bg-gray-50">
              <input v-model="offerNew.name" placeholder="اسم العرض" class="border p-2 rounded w-full"/>
              <input v-model="offerNew.price" type="number" placeholder="السعر" class="border p-2 rounded w-full"/>
              <input v-model="offerNew.image" placeholder="رابط الصورة (اختياري)" class="border p-2 rounded w-full"/>
              <input type="hidden" v-model="offerNew.game_id"/>
              <button @click="storeOffer" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">حفظ</button>
            </div>
          </div>

          <!-- تعديل العرض -->
          <div v-if="offerEditing.id && offerEditing.game_id === game.id" class="mt-2 p-2 border rounded bg-gray-50 flex flex-col gap-2">
            <input v-model="offerEditing.name" placeholder="اسم العرض" class="border p-2 rounded w-full"/>
            <input v-model="offerEditing.price" type="number" placeholder="السعر" class="border p-2 rounded w-full"/>
            <input v-model="offerEditing.image" placeholder="رابط الصورة" class="border p-2 rounded w-full"/>
            <button @click="updateOffer" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">تحديث</button>
            <button @click="cancelEditOffer" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">إلغاء</button>
          </div>

        </div>

      </div>
    </div>

  </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  games: Array
})

// ----- إدارة الألعاب -----
const showGameForm = ref(false)
const newGame = ref({ name: '', description: '', image: '' })
const gameEditing = ref({})
const selectedGame = ref({})

// ----- إدارة العروض -----
const newOfferForm = ref({})
const offerNew = ref({ game_id: '', name: '', price: '', image: '' })
const offerEditing = ref({})

// إدارة الألعاب
const storeGame = () => {
  const form = useForm(newGame.value)
  form.post('/admin/topups', {
    onSuccess: () => {
      newGame.value = { name: '', description: '', image: '' }
      showGameForm.value = false
    }
  })
}
const editGame = (game) => { gameEditing.value = { ...game } }
const cancelEditGame = () => { gameEditing.value = {} }
const updateGame = () => {
  const form = useForm(gameEditing.value)
  form.put('/admin/topups/game/' + gameEditing.value.id, { onSuccess: () => gameEditing.value = {} })
}
const destroyGame = (id) => {
  if (confirm('هل تريد حذف هذه اللعبة؟')) {
    const form = useForm({})
    form.delete('/admin/topups/game/' + id)
  }
}
const selectGame = (game) => { selectedGame.value = selectedGame.value.id === game.id ? {} : game }

// إدارة العروض
const toggleNewOfferForm = (gameId) => {
  newOfferForm.value[gameId] = !newOfferForm.value[gameId]
  offerNew.value = { game_id: gameId, name: '', price: '', image: '' }
}
const storeOffer = () => {
  const form = useForm(offerNew.value)
  form.post('/admin/topups/offer', {
    onSuccess: () => {
      newOfferForm.value[offerNew.value.game_id] = false
      offerNew.value = { game_id: '', name: '', price: '', image: '' }
    }
  })
}
const editOffer = (offer, gameId) => { offerEditing.value = { ...offer, game_id: gameId } }
const cancelEditOffer = () => { offerEditing.value = {} }
const updateOffer = () => {
  const form = useForm(offerEditing.value)
  form.put('/admin/topups/offer/' + offerEditing.value.id, { onSuccess: () => offerEditing.value = {} })
}
const destroyOffer = (id) => {
  if (confirm('هل تريد حذف هذا العرض؟')) {
    const form = useForm({})
    form.delete('/admin/topups/offer/' + id)
  }
}
</script>

<style scoped>
body { direction: rtl; }
</style>