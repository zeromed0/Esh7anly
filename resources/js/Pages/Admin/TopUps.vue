
<template>
  <AdminLayout>
  <div class="p-4">

    <h1 class="text-xl font-bold mb-4 text-gray-800">الشحنات (Top Ups)</h1>

    <!-- زر إضافة لعبة -->
    <button
      @click="showGameForm = !showGameForm"
      class="bg-blue-600 text-white px-3 py-1.5 text-sm rounded-md mb-4 hover:bg-blue-700 transition">
      {{ showGameForm ? 'إلغاء' : 'إضافة لعبة' }}
    </button>

    <!-- نموذج إضافة لعبة -->
    <div v-if="showGameForm" class="mb-4 p-3 bg-white rounded-lg shadow-sm flex flex-col gap-2 max-w-md">
      <input v-model="newGame.name" placeholder="اسم اللعبة" class="border p-1.5 text-sm rounded"/>
      <input v-model="newGame.description" placeholder="الوصف" class="border p-1.5 text-sm rounded"/>
      <input v-model="newGame.image" placeholder="رابط الصورة" class="border p-1.5 text-sm rounded"/>
      <button @click="storeGame" class="bg-green-600 text-white px-3 py-1.5 text-sm rounded hover:bg-green-700">
        حفظ
      </button>
    </div>

    <!-- قائمة الألعاب -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">

      <div v-for="game in games" :key="game.id"
           class="bg-white rounded-xl shadow-sm p-3 flex flex-col text-sm border">

        <img
          :src="game.image || '/images/default-game.png'"
          class="w-16 h-16 object-cover rounded-md mb-2 cursor-pointer mx-auto"
          @click="selectGame(game)"
        />

        <h2 class="font-semibold text-center truncate">{{ game.name }}</h2>

        <span
          class="text-xs mt-1 mx-auto px-2 py-0.5 rounded-full"
          :class="game.is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
          {{ game.is_active ? 'مفعل' : 'معطل' }}
        </span>

        <!-- أزرار اللعبة -->
        <div class="flex gap-1 mt-2 justify-center">
          <button @click="editGame(game)" class="bg-yellow-400 text-white px-2 py-1 rounded text-xs">✏</button>
          <button @click="destroyGame(game.id)" class="bg-red-500 text-white px-2 py-1 rounded text-xs">🗑</button>
          <button @click="toggleGameStatus(game)"
                  :class="game.is_active ? 'bg-gray-500' : 'bg-green-600'"
                  class="text-white px-2 py-1 rounded text-xs">
            {{ game.is_active ? 'تعطيل' : 'تفعيل' }}
          </button>
        </div>

        <!-- تعديل لعبة -->
        <div v-if="gameEditing.id === game.id" class="mt-2 flex flex-col gap-1">
          <input v-model="gameEditing.name" class="border p-1 text-xs rounded"/>
          <input v-model="gameEditing.description" class="border p-1 text-xs rounded"/>
          <input v-model="gameEditing.image" class="border p-1 text-xs rounded"/>
          <button @click="updateGame" class="bg-green-600 text-white py-1 rounded text-xs">تحديث</button>
          <button @click="cancelEditGame" class="bg-gray-400 text-white py-1 rounded text-xs">إلغاء</button>
        </div>

        <!-- العروض -->
        <div v-if="selectedGame.id === game.id" class="mt-3 border-t pt-2">

          <h3 class="font-semibold text-xs mb-2">العروض</h3>

          <div v-for="offer in game.offers" :key="offer.id"
               class="border rounded-md p-2 mb-2 bg-gray-50 text-xs">

            <div class="flex justify-between items-center">

              <div class="flex items-center gap-2">
                <img
                  :src="offer.image || '/images/default-offer.png'"
                  class="w-10 h-10 object-cover rounded"
                />
                <div>
                  <p class="font-semibold">{{ offer.name }}</p>
                  <p class="text-gray-500">{{ offer.price }}</p>
                </div>
              </div>

              <span
                :class="offer.is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'"
                class="px-2 py-0.5 rounded-full text-xs">
                {{ offer.is_active ? 'مفعل' : 'معطل' }}
              </span>

            </div>

            <div class="flex gap-1 mt-2">
              <button @click="editOffer(offer, game.id)" class="bg-yellow-400 text-white px-2 py-1 rounded text-xs">✏</button>
              <button @click="destroyOffer(offer.id)" class="bg-red-500 text-white px-2 py-1 rounded text-xs">🗑</button>
              <button @click="toggleOfferStatus(offer)"
                      :class="offer.is_active ? 'bg-gray-500' : 'bg-green-600'"
                      class="text-white px-2 py-1 rounded text-xs">
                {{ offer.is_active ? 'تعطيل' : 'تفعيل' }}
              </button>
            </div>

          </div>

          <!-- إضافة عرض -->
          <button @click="toggleNewOfferForm(game.id)"
                  class="bg-blue-600 text-white px-2 py-1 text-xs rounded w-full">
            {{ newOfferForm[game.id] ? 'إلغاء' : 'إضافة عرض' }}
          </button>

          <div v-if="newOfferForm[game.id]" class="mt-2 flex flex-col gap-1">
            <input v-model="offerNew.name" placeholder="اسم العرض" class="border p-1 text-xs rounded"/>
            <input v-model="offerNew.price" type="number" placeholder="السعر" class="border p-1 text-xs rounded"/>
            <input v-model="offerNew.image" placeholder="رابط الصورة" class="border p-1 text-xs rounded"/>
            <button @click="storeOffer" class="bg-green-600 text-white py-1 rounded text-xs">حفظ</button>
          </div>

          <!-- تعديل عرض -->
          <div v-if="offerEditing.id && offerEditing.game_id === game.id"
               class="mt-2 flex flex-col gap-1 border rounded p-2 bg-white">

            <input v-model="offerEditing.name" class="border p-1 text-xs rounded"/>
            <input v-model="offerEditing.price" type="number" class="border p-1 text-xs rounded"/>
            <input v-model="offerEditing.image" class="border p-1 text-xs rounded"/>

            <button @click="updateOffer"
                    class="bg-green-600 text-white py-1 rounded text-xs">
              تحديث
            </button>

            <button @click="cancelEditOffer"
                    class="bg-gray-400 text-white py-1 rounded text-xs">
              إلغاء
            </button>
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

defineProps({ games: Array })

const showGameForm = ref(false)
const newGame = ref({ name: '', description: '', image: '' })
const gameEditing = ref({})
const selectedGame = ref({})

const newOfferForm = ref({})
const offerNew = ref({ game_id: '', name: '', price: '', image: '' })
const offerEditing = ref({})

const storeGame = () => {
  useForm(newGame.value).post('/admin/topups')
}

const editGame = (game) => gameEditing.value = { ...game }
const cancelEditGame = () => gameEditing.value = {}

const updateGame = () => {
  useForm(gameEditing.value)
    .put('/admin/topups/game/' + gameEditing.value.id,
      { onSuccess: () => gameEditing.value = {} })
}

const destroyGame = (id) => {
  if (confirm('حذف اللعبة؟'))
    useForm({}).delete('/admin/topups/game/' + id)
}

const toggleGameStatus = (game) => {
  useForm({ is_active: !game.is_active })
    .put('/admin/topups/game/' + game.id + '/toggle')
}

const selectGame = (game) =>
  selectedGame.value = selectedGame.value.id === game.id ? {} : game

const toggleNewOfferForm = (gameId) => {
  newOfferForm.value[gameId] = !newOfferForm.value[gameId]
  offerNew.value = { game_id: gameId, name: '', price: '', image: '' }
}

const storeOffer = () => {
  useForm(offerNew.value).post('/admin/topups/offer')
}

const editOffer = (offer, gameId) =>
  offerEditing.value = { ...offer, game_id: gameId }

const cancelEditOffer = () => offerEditing.value = {}

const updateOffer = () => {
  useForm(offerEditing.value)
    .put('/admin/topups/offer/' + offerEditing.value.id,
      { onSuccess: () => offerEditing.value = {} })
}

const destroyOffer = (id) => {
  if (confirm('حذف العرض؟'))
    useForm({}).delete('/admin/topups/offer/' + id)
}

const toggleOfferStatus = (offer) => {
  useForm({ is_active: !offer.is_active })
    .put('/admin/topups/offer/' + offer.id + '/toggle')
}
</script>

<style scoped>
body { direction: rtl; }
</style>