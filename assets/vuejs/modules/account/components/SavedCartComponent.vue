<template>
  <div v-if="savedCart" class="bloc-cart-saved">
    <div class="md:w-5/12">
      <RouterLink
        :to="{
          name: PageList.SAVED_CARTS_DETAILS,
          params: { id: savedCart.id },
        }"
        class="font-bold text-primary underline"
        @click="sendGaEvent('click_saved_carts_details')"
      >
        {{ savedCart.name }}
      </RouterLink>
    </div>
    <div class="md:w-2/12">
      {{ createdAd }}
    </div>
    <div class="md:w-3/12">
      {{ nbProducts }}
    </div>
    <div class="flex justify-end md:w-2/12">
      <div class="flex">
        <RouterLink
          :to="{
            name: PageList.SAVED_CARTS_DETAILS,
            params: { id: savedCart.id },
          }"
          class="flex"
          title="Visualisez le contenu du panier sauvegardé"
          @click="sendGaEvent('click_saved_carts_details')"
        >
          <EyeIconComponent :stroke="channelPrimaryColor" class="mr-2" />
        </RouterLink>
        <button
          :disabled="isNeoAutoLogin"
          class="flex"
          title="Ajoutez ce panier sauvegardé à votre panier actuel"
          @click="openAddToCartConfirm"
        >
          <ShoppingCartIconComponent
            :stroke="channelPrimaryColor"
            class="mr-2"
          />
        </button>
        <button
          :disabled="isNeoAutoLogin"
          class="flex"
          title="Supprimez ce panier sauvegardé"
          @click="openDeleteForm"
        >
          <TrashIconComponent :stroke="channelPrimaryColor" />
        </button>
      </div>
      <SavedCartModal
        v-if="showForm"
        :is-loading="isLoading"
        class="modal"
        @cancel="showForm = false"
        @submit-saved-cart="onSubmitSavedCart"
      />
      <SavedCartDeleteModal
        v-if="showDeleteForm"
        :is-loading="isLoading"
        :saved-cart-id="savedCart.id"
        class="modal"
        @cancel="showDeleteForm = false"
        @delete-saved-cart="onDelete"
      />
      <SavedCartAddToCartModal
        v-if="showAddToCartConfirm"
        :is-loading="isLoading"
        :saved-cart-id="savedCart.id"
        class="modal"
        @cancel="showAddToCartConfirm = false"
        @add-to-cart="onAddToCart"
      />
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { PageList } from '@/vuejs/router'
import { format } from 'date-fns'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import SavedCartModal from '@/vuejs/modules/account/components/savedCart/SavedCartModal.vue'
import EyeIconComponent from '@/vuejs/modules/shared/icon/EyeIconComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import SavedCartDeleteModal from '@/vuejs/modules/account/components/savedCart/SavedCartDeleteModal.vue'
import SavedCartAddToCartModal from '@/vuejs/modules/account/components/savedCart/SavedCartAddToCartModal.vue'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useUserStore } from '@/vuejs/stores/user'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const props = defineProps({
  savedCart: {
    required: true,
    type: Object,
  },
})

const { isNeoAutoLogin } = storeToRefs(useUserStore())
const { channelPrimaryColor } = storeToRefs(useChannelStore())

const emit = defineEmits(['submit', 'delete', 'addToCart'])

const showForm = ref<boolean>(false)
const showDeleteForm = ref<boolean>(false)
const showAddToCartConfirm = ref<boolean>(false)
const isLoading = ref<boolean>(false)

const createdAd = computed(() => {
  return format(new Date(props.savedCart.createdAt), 'dd/MM/yyyy')
})

const nbProducts = computed(() => {
  return props.savedCart.savedCartProducts.length
})

const openDeleteForm = () => {
  showDeleteForm.value = true
  sendGaEvent('click_saved_carts_delete')
}

const openAddToCartConfirm = () => {
  showAddToCartConfirm.value = true
  sendGaEvent('click_saved_carts_edit')
}
const onSubmitSavedCart = (event) => {
  emit('submit', {
    savedCart: event.savedCart,
  })
  showForm.value = false
}

const onDelete = (event) => {
  emit('delete', {
    savedCartId: event.savedCartId,
  })

  showDeleteForm.value = false
}

const onAddToCart = (event) => {
  emit('addToCart', {
    savedCartId: event.savedCartId,
  })

  showDeleteForm.value = false
}
</script>
<style scoped>
.bloc-cart-saved {
  @apply mb-4 flex w-[48.5%] flex-col rounded-lg bg-white p-2.5 text-sm md:w-full md:flex-row md:text-base lg:text-lg;
}
</style>
