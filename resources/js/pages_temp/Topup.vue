<template>
  <UserLayout :user="user">
    <div class="p-4 max-w-4xl mx-auto">
  <!-- العنوان -->
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-lg font-semibold text-blue-700">Available Games</h2>
    <button
      class="text-blue-600 hover:text-blue-800 text-xs font-medium"
      @click="selectedGame = null"
    >
      Reset ↻
    </button>
  </div>

  <!-- شبكة الألعاب (محسّنة للهاتف) -->
  <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2 mb-6">
    <div
      v-for="game in games"
      :key="game.id"
      @click="selectGame(game)"
      class="bg-white/80 backdrop-blur-sm rounded-xl shadow-sm hover:shadow-md p-2 cursor-pointer transition hover:-translate-y-1 flex flex-col items-center border"
      :class="selectedGame && selectedGame.id === game.id ? 'border-blue-600' : 'border-transparent'"
    >
      <img
        :src="game.image || '/images/default-game.jpg'"
        class="w-10 h-10 rounded-full object-cover mb-1"
      />
      <p class="text-xs font-semibold text-gray-700 text-center truncate w-full">
        {{ game.name }}
      </p>
    </div>
  </div>

  <!-- تفاصيل اللعبة -->
  <div v-if="selectedGame" class="bg-white rounded-xl shadow-md p-4 animate-fadeIn">
    <h3 class="text-lg font-semibold text-blue-700 mb-1">{{ selectedGame.name }}</h3>
    <p class="text-gray-600 text-sm mb-4">
      {{ selectedGame.description || "No description available." }}
    </p>

    <!-- عروض الشحن (محسّنة للهاتف) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-4">
      <div
        v-for="offer in selectedGame.offers"
        :key="offer.id"
        @click="selectOffer(offer)"
        class="border rounded-xl p-3 text-center cursor-pointer hover:shadow-md transition"
        :class="selectedOffer && selectedOffer.id === offer.id ? 'border-blue-600 bg-blue-50' : 'border-gray-200'"
      >
        <img
          :src="offer.image || '/images/default-offer.jpg'"
          class="w-16 h-16 rounded-full object-cover mx-auto mb-2"
        />
        <p class="font-semibold text-blue-500 text-sm">{{ offer.name }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ formatCurrency(offer.price) }}</p>
      </div>
    </div>

        <!-- إدخال ID اللاعب -->
        <div class="mb-6">
          <label class="block text-gray-700 mb-1 font-medium">Player ID</label>
          <input
            v-model="playerId"
            type="text"
            placeholder="Enter your player ID..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
          />
        </div>

        <!-- زر تنفيذ الطلب -->
        <div class="text-right">
          <button
            @click="submitOrder"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition"
          >
            Confirm Order
          </button>
        </div>

        <!-- رسائل -->
        <p v-if="message" class="mt-4 text-sm font-medium" :class="messageClass">{{ message }}</p>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref } from "vue"
import { router } from "@inertiajs/vue3"
import UserLayout from "@/Layouts/UserLayout.vue"

const props = defineProps({
  user: Object,
  games: Array,
})

const selectedGame = ref(null)
const selectedOffer = ref(null)
const playerId = ref("")
const message = ref("")
const messageClass = ref("text-green-600")

const selectGame = (game) => {
  selectedGame.value = game
  selectedOffer.value = null
  message.value = ""
}

const selectOffer = (offer) => {
  selectedOffer.value = offer
  message.value = ""
}

const formatCurrency = (value) =>
  new Intl.NumberFormat("en-US", { style: "currency", currency: "MRU" }).format(value || 0)

const submitOrder = async () => {
  if (!selectedGame.value || !selectedOffer.value) {
    message.value = "Please select a game and offer first."
    messageClass.value = "text-red-600"
    return
  }
  if (!playerId.value) {
    message.value = "Please enter your player ID."
    messageClass.value = "text-red-600"
    return
  }

  try {
    await router.post(
      "/user/topup",
      {
        game_id: selectedGame.value.id,
        offer_id: selectedOffer.value.id,
        player_id: playerId.value,
        quantity: 1, // 👈 الكمية ثابتة دائمًا 1
      },
      {
        onSuccess: () => {
          message.value = "✅ Order submitted successfully!"
          messageClass.value = "text-green-600"
          selectedOffer.value = null
          playerId.value = ""
        },
        onError: (errors) => {
          message.value = errors.wallet_balance
            ? errors.wallet_balance
            : "Failed to submit order. Please try again."
          messageClass.value = "text-red-600"
        },
      }
    )
  } catch (e) {
    message.value = "Something went wrong."
    messageClass.value = "text-red-600"
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(15px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fadeIn {
  animation: fadeIn 0.3s ease;
}
</style>