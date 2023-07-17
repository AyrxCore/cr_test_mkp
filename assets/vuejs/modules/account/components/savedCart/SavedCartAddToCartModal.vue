<template>
  <DefaultModal @cancel="onCancelClick">
    <template #title>
      Ajouter les produits de cette liste dans le panier
    </template>
    <template #content>
      <div class="px-5">
        <div v-if="savedCart">
          <h4 class="text-sm text-white md:text-base lg:text-lg">
            Voulez-vous vraiment ajouter la liste
            <strong>{{ savedCart.name }}</strong>
            contenant {{ savedCart.savedCartProducts.length }} référence(s) dans
            votre panier
          </h4>
        </div>

        <div class="mt-5 flex justify-end">
          <ButtonComponent
            class="button-secondary !hover:border mr-2 !border"
            type="button"
            @click="onCancelClick"
          >
            Annuler
          </ButtonComponent>
          <ButtonComponent
            class="button-primary !border-0 hover:!bg-primary focus:!bg-primary"
            :is-loading="isLoading"
            @click="onAddToCart"
          >
            <ShoppingCartIconComponent />
            Ajouter
          </ButtonComponent>
        </div>
      </div>
    </template>
  </DefaultModal>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { onMounted, ref } from 'vue'
import DefaultModal from '@/vuejs/modules/account/pages/DefaultModalPage.vue'
import { SavedCart } from '@/vuejs/types/SavedCart'
import { useSavedCartStore } from '@/vuejs/stores/savedCart'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'

const props = defineProps({
  savedCartId: {
    type: String,
    required: false,
    default: null,
  },
  isLoading: {
    type: Boolean,
    required: false,
    default: false,
  },
})
const savedCart = ref<SavedCart>()
const savedCartStore = useSavedCartStore()
const emit = defineEmits(['cancel', 'addToCart'])

onMounted(() => {
  savedCart.value = savedCartStore.savedCarts.find(
    (sc) => sc.id === props.savedCartId,
  )
})

const onCancelClick = () => {
  emit('cancel')
}

const onAddToCart = async () => {
  await emit('addToCart', {
    savedCartId: props.savedCartId,
  })
}
</script>

<style scoped></style>
