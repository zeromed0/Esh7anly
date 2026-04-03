
<template>
  <UserLayout :user="user">
    <div class="p-6 md:p-8 max-w-6xl mx-auto">

      <!-- العنوان -->
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-blue-700">Voucher Offers</h2>
        <button
          class="text-blue-600 hover:text-blue-800 text-sm font-medium"
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
          @click="selectGame(game)"
          class="relative bg-white/80 backdrop-blur-sm rounded-xl shadow-sm p-2 flex flex-col items-center border transition"
          :class="[
            selectedGame && selectedGame.id === game.id ? 'border-blue-600' : 'border-transparent',
            !game.is_active ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer hover:-translate-y-1 hover:shadow-md'
          ]"
        >
          <!-- Disabled -->
          <div
            v-if="!game.is_active"
            class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-xl flex items-center justify-center"
          >
            <span class="bg-red-600 text-white text-[10px] px-2 py-1 rounded-full">
              Disabled
            </span>
          </div>

          <img
            :src="game.image || '/images/default-game.jpg'"
            class="w-10 h-10 rounded-xl object-cover mb-1"
          />
          <p class="text-xs font-semibold text-gray-700 text-center truncate w-full">
            {{ game.name }}
          </p>
        </div>
      </div>

      <!-- تفاصيل اللعبة -->
      <div v-if="selectedGame" class="bg-white rounded-xl shadow-md p-4 animate-fadeIn">
        <h3 class="text-lg font-semibold text-blue-700 mb-1">{{ selectedGame.name }}</h3>
        <p class="text-gray-600 text-sm mb-4">{{ selectedGame.description || "No description available." }}</p>

        <!-- العروض -->
        <div class="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-4">
          <div
            v-for="offer in selectedGame.offers"
            :key="offer.id"
            @click="selectOffer(offer)"
            class="relative border rounded-xl p-2 text-center transition"
            :class="[
              selectedOffer && selectedOffer.id === offer.id ? 'border-blue-600 bg-blue-50' : 'border-gray-200',
              (!offer.is_active || !selectedGame.is_active || offer.stock === 0) ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer hover:shadow-md'
            ]"
          >
            <!-- Disabled -->
            <div
              v-if="!offer.is_active || !selectedGame.is_active"
              class="absolute inset-0 bg-white/80 backdrop-blur-sm rounded-xl flex items-center justify-center"
            >
              <span class="bg-red-600 text-white text-[10px] px-2 py-1 rounded-full">
                Disabled
              </span>
            </div>

            <!-- Out of Stock -->
            <div
              v-if="offer.stock === 0"
              class="absolute inset-0 bg-white/20 rounded-full flex items-center justify-center"
            >
              <span class="bg-gray-800 text-white text-[10px] px-2 py-1 rounded-full">
                Out of Stock
              </span>
            </div>

            <img
              :src="offer.image || '/images/default-offer.jpg'"
              class="w-12 h-12 rounded-xl object-cover mx-auto mb-1"
            />
            <p class="font-semibold text-blue-500 text-sm">{{ offer.name }}</p>
            <p class="text-xs text-gray-500 mt-">{{ formatCurrency(offer.price) }}</p>
            <p v-if="offer.stock > 0" class="text-xs text-gray-400 mt-0">{{ offer.stock }} codes left</p>
          </div>
        </div>

        <!-- الكمية -->
        <div class="mb-6">
          <label class="block text-gray-700 mb-1 font-medium">Quantity</label>
          <input
            v-model.number="quantity"
            type="number"
            min="1"
            :max="selectedOffer ? selectedOffer.stock : null"
            class="w-full md:w-1/3 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 outline-none"
          />
        </div>

        <!-- السعر -->
        <div v-if="selectedOffer" class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
          <div class="flex justify-between text-sm mb-1">
            <span>Price</span>
            <span>{{ formatCurrency(selectedOffer.price * quantity) }}</span>
          </div>
          <div class="flex justify-between text-sm mb-1">
            <span>Tax ({{ tax_percent ?? 0 }}%)</span>
            <span>{{ formatCurrency(taxAmount) }}</span>
          </div>
          <div class="flex justify-between font-semibold text-blue-700 mt-2">
            <span>Total</span>
            <span>{{ formatCurrency(finalPrice) }}</span>
          </div>
        </div>

        <!-- زر الشراء -->
        <div class="text-right">
          <button
            @click="submitVoucherOrder"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition"
          >
            Buy Now
          </button>
        </div>

        <!-- الرسائل -->
        <p v-if="message" class="mt-4 text-sm font-medium" :class="messageClass">{{ message }}</p>
      </div>

      <!-- Modal الأكواد -->
      <div
        v-if="showPurchasedCodes"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      >
        <div class="bg-white rounded-lg p-6 w-11/12 md:w-1/2 lg:w-1/3 shadow-lg animate-fadeIn">
          <h3 class="text-lg font-semibold text-blue-700 mb-4">🎉 Your Voucher Codes</h3>
          <textarea
            readonly
            class="w-full border rounded-lg p-3 mb-4 h-40 resize-none"
          >{{ purchasedCodes.join('\n') }}</textarea>
          <div class="text-right">
            <button
              @click="showPurchasedCodes = false"
              class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
            >
              OK
            </button>
          </div>
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
const quantity = ref(1)
const message = ref("")
const messageClass = ref("text-green-600")

const purchasedCodes = ref([])
const showPurchasedCodes = ref(false)

const taxAmount = computed(() => {
  if (!selectedOffer.value) return 0
  return (selectedOffer.value.price * quantity.value) * (props.tax_percent / 100)
})

const finalPrice = computed(() => {
  if (!selectedOffer.value) return 0
  return (selectedOffer.value.price * quantity.value) + taxAmount.value
})

const resetSelection = () => {
  selectedGame.value = null
  selectedOffer.value = null
  quantity.value = 1
}

const selectGame = (game) => {
  if (!game.is_active) return
  selectedGame.value = game
  selectedOffer.value = null
  message.value = ""
}

const selectOffer = (offer) => {
  if (!offer.is_active || offer.stock === 0) return
  selectedOffer.value = offer
  message.value = ""
}

const formatCurrency = (value) =>
  new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "MRU"
  }).format(value || 0)

const submitVoucherOrder = () => {

  if (!selectedGame.value || !selectedOffer.value) {
    message.value = "Please select a game and offer first."
    messageClass.value = "text-red-600"
    return
  }

  router.post("/user/vouchers", {
    game_id: selectedGame.value.id,
    offer_id: selectedOffer.value.id,
    quantity: quantity.value
  }, {

    onSuccess: (page) => {

      purchasedCodes.value = page.props.codes || []
      showPurchasedCodes.value = true

      quantity.value = 1
      selectedOffer.value = null

      message.value = ""
    },

    onError: (errors) => {

      if (errors.limit) {
        message.value = "تم الوصول للحد اليومي يرجى توثيق حسابك"
      }
      else if (errors.error) {
        message.value = errors.error
      }
      else {
        message.value = "Failed to process your request."
      }

      messageClass.value = "text-red-600"
    }

  })
}
</script>