
<template>
  <UserLayout :user="user">
    <div class="p-4 max-w-4xl mx-auto">

      <!-- العنوان -->
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-blue-700">Top-Up Offers</h2>
        <button
          class="text-blue-600 hover:text-blue-800 text-xs font-medium"
          @click="resetSelection"
        >
          Reset ↻
        </button>
      </div>

      <!-- شبكة الألعاب -->
      <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2 mb-6">
        <div
          v-for="game in games"
          :key="game.id"
          @click="game.is_active && selectGame(game)"
          class="relative bg-white/80 backdrop-blur-sm rounded-xl shadow-sm p-2 transition flex flex-col items-center border"
          :class="[
            selectedGame && selectedGame.id === game.id ? 'border-blue-600' : 'border-transparent',
            game.is_active ? 'hover:shadow-md hover:-translate-y-1 cursor-pointer' : 'opacity-50 cursor-not-allowed pointer-events-none'
          ]"
        >
          <div
            v-if="!game.is_active"
            class="absolute inset-7 bg-white/80 backdrop-blur-sm rounded-xl flex items-center justify-center z-3"
          >
            <span class="bg-red-600 text-white text-[10px] px-2 py-1 rounded-full">
              Disabled
            </span>
          </div>
          <img :src="game.image || '/images/default-game.jpg'" class="w-10 h-10 rounded-full object-cover mb-1"/>
          <p class="text-xs font-semibold text-gray-700 text-center truncate w-full">
            {{ game.name }}
          </p>
        </div>
      </div>

      <!-- تفاصيل اللعبة -->
      <div v-if="selectedGame" class="bg-white rounded-xl shadow-md p-4 animate-fadeIn">
        <h3 class="text-lg font-semibold text-blue-700 mb-1">{{ selectedGame.name }}</h3>
        <p class="text-gray-600 text-sm mb-4">{{ selectedGame.description || "No description available." }}</p>

        <!-- عروض الشحن -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-4">
          <div
            v-for="offer in selectedGame.offers"
            :key="offer.id"
            @click="offer.is_active && selectedGame.is_active && selectOffer(offer)"
            class="relative border rounded-xl p-3 text-center transition"
            :class="[
              selectedOffer && selectedOffer.id === offer.id ? 'border-blue-600 bg-blue-50' : 'border-gray-200',
              offer.is_active && selectedGame.is_active ? 'cursor-pointer hover:shadow-md' : 'opacity-50 cursor-not-allowed pointer-events-none'
            ]"
          >
            <div
              v-if="!offer.is_active || !selectedGame.is_active"
              class="absolute inset-13 bg-white/80 backdrop-blur-sm rounded-xl flex items-center justify-center z-3"
            >
              <span class="bg-red-600 text-white text-[10px] px-2 py-1 rounded-full">Disabled</span>
            </div>
            <img :src="offer.image || '/images/default-offer.jpg'" class="w-16 h-16 rounded-full object-cover mx-auto mb-2"/>
            <p class="font-semibold text-blue-500 text-sm">{{ offer.name }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ formatCurrency(offer.price) }}</p>
          </div>
        </div>

        <!-- Player ID -->
        <div class="mb-4">
          <label class="block text-gray-700 mb-1 font-medium">Player ID</label>
          <input
            v-model="playerId"
            type="text"
            placeholder="Enter your player ID..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
          />
        </div>

        <!-- السعر + الضريبة + المجموع -->
        <div v-if="selectedOffer" class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
          <div class="flex justify-between text-sm mb-1">
            <span>Price</span>
            <span>{{ formatCurrency(Number(selectedOffer.price)) }}</span>
          </div>
          <div class="flex justify-between text-sm mb-1">
            <span>Tax ({{ taxPercent }}%)</span>
            <span>{{ formatCurrency(taxAmount) }}</span>
          </div>
          <div class="flex justify-between font-semibold text-blue-700 mt-2">
            <span>Total</span>
            <span>{{ formatCurrency(finalPrice) }}</span>
          </div>
        </div>

        <!-- زر تنفيذ الطلب -->
        <div class="text-right">
          <button
            @click="submitOrder"
            :disabled="!selectedGame || !selectedOffer || !selectedGame.is_active || !selectedOffer.is_active || !playerId"
            class="font-semibold px-6 py-2 rounded-lg shadow transition"
            :class="!selectedGame || !selectedOffer || !selectedGame.is_active || !selectedOffer.is_active || !playerId
              ? 'bg-gray-400 cursor-not-allowed text-white'
              : 'bg-blue-600 hover:bg-blue-700 text-white'"
          >
            Confirm Order
          </button>
        </div>

        <!-- رسائل -->
        <p v-if="message" class="mt-4 text-sm font-medium" :class="messageClass">{{ message }}</p>
      </div>

      <!-- Modal احترافي -->
      <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 animate-fadeIn">
          <h3 class="text-xl font-bold text-blue-700 mb-4">✅ Order Confirmation</h3>
          <table class="w-full text-sm mb-4">
            <tr>
              <td class="font-semibold py-1">Game:</td>
              <td class="text-right">{{ selectedGame.name }}</td>
            </tr>
            <tr>
              <td class="font-semibold py-1">Offer:</td>
              <td class="text-right">{{ selectedOffer.name }}</td>
            </tr>
            <tr>
              <td class="font-semibold py-1">Player ID:</td>
              <td class="text-right">{{ playerId }}</td>
            </tr>
            <tr>
              <td class="font-semibold py-1">Price:</td>
              <td class="text-right">{{ formatCurrency(Number(selectedOffer.price)) }}</td>
            </tr>
            <tr>
              <td class="font-semibold py-1">Tax:</td>
              <td class="text-right">{{ formatCurrency(taxAmount) }}</td>
            </tr>
            <tr class="border-t mt-2">
              <td class="font-bold py-2">Total:</td>
              <td class="font-bold text-blue-700 text-right py-2">{{ formatCurrency(finalPrice) }}</td>
            </tr>
          </table>
          <button
            @click="closeModal"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg w-full"
          >
            موافق
          </button>
        </div>
      </div>

    </div>
  </UserLayout>
</template>

<script setup>
import { ref, computed } from "vue"
import { router } from "@inertiajs/vue3"
import UserLayout from "@/Layouts/UserLayout.vue"

const props = defineProps({
  user: Object,
  games: Array,
  tax_percent: Number
})

const selectedGame = ref(null)
const selectedOffer = ref(null)
const playerId = ref("")
const message = ref("")
const messageClass = ref("text-green-600")
const showModal = ref(false)

// حساب الضريبة والمجموع النهائي
const taxPercent = computed(() => Number(props.tax_percent ?? 0))
const taxAmount = computed(() =>
  selectedOffer.value ? Number(selectedOffer.value.price) * (taxPercent.value / 100) : 0
)
const finalPrice = computed(() =>
  selectedOffer.value ? Number(selectedOffer.value.price) + taxAmount.value : 0
)

const selectGame = (game) => {
  if (!game.is_active) return
  selectedGame.value = game
  selectedOffer.value = null
  message.value = ""
}

const selectOffer = (offer) => {
  if (!offer.is_active || !selectedGame.value?.is_active) return
  selectedOffer.value = offer
  message.value = ""
}

const resetSelection = () => {
  selectedGame.value = null
  selectedOffer.value = null
  playerId.value = ""
  message.value = ""
}

const formatCurrency = (value) =>
  new Intl.NumberFormat("en-US", { style: "currency", currency: "MRU" }).format(value || 0)

const closeModal = () => {
  showModal.value = false
  selectedOffer.value = null
  playerId.value = ""
}

const submitOrder = async () => {
  if (!selectedGame.value || !selectedOffer.value) {
    message.value = "Please select a game and offer first."
    messageClass.value = "text-red-600"
    return
  }
  if (!selectedGame.value.is_active || !selectedOffer.value.is_active) {
    message.value = "This game or offer is currently disabled."
    messageClass.value = "text-red-600"
    return
  }
  if (!playerId.value) {
    message.value = "Please enter your player ID."
    messageClass.value = "text-red-600"
    return
  }

  try {
    // ارسال الطلب إلى السيرفر
    await router.post("/user/topup", {
      game_id: selectedGame.value.id,
      offer_id: selectedOffer.value.id,
      player_id: playerId.value,
      quantity: 1
    })
    showModal.value = true
    message.value = ""
  } catch (e) {
    message.value = "حدث خطأ أثناء تنفيذ الطلب."
    messageClass.value = "text-red-600"
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.3s ease; }
</style>