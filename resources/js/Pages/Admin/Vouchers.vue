
<template>
  <AdminLayout>
    <div class="p-6 max-w-7xl mx-auto">

      <!-- العنوان -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Voucher Management</h1>

        <button
          @click="showGameForm = !showGameForm"
          class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow transition"
        >
          {{ showGameForm ? 'Cancel' : 'Add New Game' }}
        </button>
      </div>

      <!-- نموذج اللعبة -->
      <div v-if="showGameForm || gameEditing.id"
        class="bg-white p-6 rounded-xl shadow mb-8 border">

        <h2 class="text-lg font-semibold mb-4 text-gray-700">
          {{ gameEditing.id ? 'Edit Game' : 'Create Game' }}
        </h2>

        <div class="grid md:grid-cols-3 gap-4">

          <input
            v-model="gameForm.name"
            placeholder="Game name"
            class="border rounded-lg p-2 w-full"
          />

          <input
            v-model="gameForm.description"
            placeholder="Description"
            class="border rounded-lg p-2 w-full"
          />

          <input
            v-model="gameForm.image"
            placeholder="Image URL"
            class="border rounded-lg p-2 w-full"
          />

        </div>

        <div class="flex gap-3 mt-4">

          <button
            @click="gameEditing.id ? updateGame() : storeGame()"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg"
          >
            Save
          </button>

          <button
            v-if="gameEditing.id"
            @click="cancelEditGame"
            class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg"
          >
            Cancel
          </button>

        </div>

      </div>


      <!-- الألعاب -->
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6 mb-10">

        <div
          v-for="game in games"
          :key="game.id"
          class="bg-white rounded-xl shadow hover:shadow-lg transition cursor-pointer relative p-4 border flex flex-col items-center"
          @click="selectGame(game)"
        >

          <img
            :src="game.image || '/images/default-game.png'"
            class="w-20 h-20 rounded-full object-cover mb-3"
          />

          <h2 class="font-semibold text-center">{{ game.name }}</h2>

          <p class="text-xs text-gray-500 text-center mb-3">
            {{ game.description }}
          </p>

          <!-- حالة التفعيل -->
          <button
            @click.stop="toggleGameActive(game)"
            :class="game.is_active ? 'bg-green-600' : 'bg-gray-400'"
            class="absolute top-2 right-2 text-white text-xs px-2 py-1 rounded"
          >
            {{ game.is_active ? 'Active' : 'Disabled' }}
          </button>

          <div class="flex gap-2 mt-2">

            <button
              @click.stop="editGame(game)"
              class="text-xs bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded"
            >
              Edit
            </button>

            <button
              @click.stop="destroyGame(game.id)"
              class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
            >
              Delete
            </button>

          </div>

        </div>

      </div>



      <!-- العروض -->
      <section v-if="selectedGame.id">

        <h2 class="text-xl font-semibold mb-6">
          Offers - {{ selectedGame.name }}
        </h2>


        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">

          <div
            v-for="offer in selectedGame.offers"
            :key="offer.id"
            class="bg-white rounded-xl shadow hover:shadow-lg transition p-4 border relative flex flex-col items-center"
          >

            <img
              :src="offer.image || '/images/default-offer.png'"
              class="w-20 h-20 rounded-full object-cover mb-2"
            />

            <h3 class="font-semibold text-center">{{ offer.name }}</h3>

            <p class="text-blue-600 text-sm mb-1">
              {{ offer.price }} MRU
            </p>

            <p class="text-xs text-gray-500 mb-2">
              Codes: {{ offer.voucherCodes?.length || 0 }}
            </p>


            <div class="flex gap-2 flex-wrap justify-center">

              <button
                @click.stop="toggleOfferActive(offer)"
                :class="offer.is_active ? 'bg-green-600' : 'bg-gray-400'"
                class="text-white px-3 py-1 text-xs rounded"
              >
                {{ offer.is_active ? 'Active' : 'Disabled' }}
              </button>

              <button
                @click.stop="editOffer(offer)"
                class="bg-yellow-400 text-white px-3 py-1 text-xs rounded"
              >
                Edit
              </button>

              <button
                @click.stop="destroyOffer(offer.id)"
                class="bg-red-500 text-white px-3 py-1 text-xs rounded"
              >
                Delete
              </button>

              <button
                @click.stop="selectOffer(offer)"
                class="bg-blue-600 text-white px-3 py-1 text-xs rounded"
              >
                Codes
              </button>

            </div>


            <!-- إدارة الأكواد -->
            <div
              v-if="selectedOffer.id === offer.id"
              class="w-full mt-4 bg-gray-50 p-3 rounded border"
            >

              <h4 class="font-semibold text-center mb-2">
                Voucher Codes
              </h4>


              <textarea
                v-model="newCode"
                placeholder="Enter multiple codes, each in a new line"
                class="border rounded p-2 w-full h-24 mb-2"
              ></textarea>

              <button
                @click="addCode()"
                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded"
              >
                Add Codes
              </button>


              <ul
                v-if="selectedOffer.voucherCodes?.length"
                class="max-h-40 overflow-y-auto mt-3"
              >

                <li
                  v-for="(code,i) in selectedOffer.voucherCodes"
                  :key="code.id"
                  class="flex justify-between items-center bg-white border rounded p-2 mb-1"
                >

                  <span class="text-sm">{{ code.code }}</span>

                  <button
                    @click="removeCode(code.id,i)"
                    class="text-xs bg-red-500 text-white px-2 py-1 rounded"
                  >
                    Delete
                  </button>

                </li>

              </ul>

              <p v-else class="text-xs text-gray-500 text-center mt-3">
                No codes yet
              </p>

              <button
                @click="deselectOffer"
                class="mt-3 w-full bg-gray-400 hover:bg-gray-500 text-white py-2 rounded"
              >
                Close
              </button>

            </div>

          </div>

        </div>



        <!-- إضافة عرض -->
        <div class="mt-8 bg-gray-50 border p-6 rounded-xl">

          <h3 class="text-lg font-semibold mb-4">
            {{ offerEditing.id ? 'Edit Offer' : 'Create Offer' }}
          </h3>

          <div class="grid md:grid-cols-3 gap-4">

            <input
              v-model="offerForm.name"
              placeholder="Offer name"
              class="border rounded p-2"
            />

            <input
              v-model="offerForm.price"
              type="number"
              placeholder="Price"
              class="border rounded p-2"
            />

            <input
              v-model="offerForm.image"
              placeholder="Image URL"
              class="border rounded p-2"
            />

          </div>

          <div class="flex gap-3 mt-4">

            <button
              @click="offerEditing.id ? updateOffer() : storeOffer()"
              class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded"
            >
              Save Offer
            </button>

            <button
              v-if="offerEditing.id"
              @click="cancelEditOffer"
              class="bg-gray-400 hover:bg-gray-500 text-white px-5 py-2 rounded"
            >
              Cancel
            </button>

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

const props = defineProps({ games:Array })

const showGameForm = ref(false)
const gameEditing = ref({})
const selectedGame = ref({})
const gameForm = ref({name:'',description:'',image:''})

const offerEditing = ref({})
const offerForm = ref({game_id:'',name:'',price:'',image:''})
const selectedOffer = ref({})
const newCode = ref('')

const storeGame=()=>{useForm(gameForm.value).post('/admin/vouchers',{onSuccess:()=>{gameForm.value={name:'',description:'',image:''};showGameForm.value=false}})}
const updateGame=()=>{useForm(gameForm.value).put('/admin/vouchers/game/'+gameEditing.value.id)}
const cancelEditGame=()=>{gameEditing.value={};gameForm.value={name:'',description:'',image:''}}

const selectGame=(game)=>{selectedGame.value=selectedGame.value.id===game.id?{}:game}

const toggleGameActive=(game)=>{useForm({}).post('/admin/vouchers/game/'+game.id+'/toggle-active',{onSuccess:()=>game.is_active=!game.is_active})}

const storeOffer=()=>{
if(!selectedGame.value.id)return
offerForm.value.game_id=selectedGame.value.id
useForm(offerForm.value).post('/admin/vouchers/offer')
}

const updateOffer=()=>{useForm(offerForm.value).put('/admin/vouchers/offer/'+offerEditing.value.id)}

const editOffer=(offer)=>{offerEditing.value={...offer};offerForm.value={...offer}}

const cancelEditOffer=()=>{offerEditing.value={};offerForm.value={game_id:'',name:'',price:'',image:''}}

const destroyOffer=(id)=>{if(confirm('Delete offer?'))useForm({}).delete('/admin/vouchers/offer/'+id)}

const toggleOfferActive=(offer)=>{useForm({}).post('/admin/vouchers/offer/'+offer.id+'/toggle-active',{onSuccess:()=>offer.is_active=!offer.is_active})}

const selectOffer=async(offer)=>{
if(selectedOffer.value.id===offer.id){selectedOffer.value={};return}
selectedOffer.value={...offer}

const res=await fetch('/admin/vouchers/offer/'+offer.id+'/codes')
const data=await res.json()
selectedOffer.value.voucherCodes=data.codes
}

const deselectOffer=()=>{selectedOffer.value={};newCode.value=''}

const addCode = () => {

    if (!newCode.value) return

    const form = useForm({
        codes: newCode.value
    })

    form.post('/admin/vouchers/offer/' + selectedOffer.value.id + '/codes', {

        onSuccess: () => {

            newCode.value = ''

            toast('تم إضافة الكود')

        }

    })

}

const removeCode=(id,index)=>{
useForm({}).delete('/admin/vouchers/offer/'+selectedOffer.value.id+'/codes/'+id,{
onSuccess:()=>selectedOffer.value.voucherCodes.splice(index,1)
})
}

const editGame=(game)=>{
gameEditing.value={...game}
gameForm.value={name:game.name,description:game.description,image:game.image}
showGameForm.value=true
}

const destroyGame=(id)=>{
if(confirm('Delete game?'))useForm({}).delete('/admin/vouchers/game/'+id)
}

const toast = (msg) => {
    const el = document.createElement('div')

    el.innerText = msg

    el.style.position = 'fixed'
    el.style.top = '20px'
    el.style.right = '20px'
    el.style.background = '#16a34a'
    el.style.color = 'white'
    el.style.padding = '10px 16px'
    el.style.borderRadius = '8px'
    el.style.fontSize = '14px'
    el.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)'
    el.style.zIndex = '9999'

    document.body.appendChild(el)

    setTimeout(() => {
        el.remove()
    }, 3000)
}

</script>