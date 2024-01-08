<template>
  <DefaultModal @cancel="onCancelClick">
    <template #title> Sauvegarder et commencer un nouveau panier</template>
    <template #content>
      <form v-if="savedCart" class="w-full" @submit.prevent="onSubmit">
        <div class="px-3 md:px-8">
          <SavedCartForm :saved-cart="savedCart" />
          <div class="flex justify-between md:justify-end">
            <ButtonComponent
              class="button-primary-outline mr-2"
              type="button"
              @click="onCancelClick"
            >
              Annuler
            </ButtonComponent>
            <ButtonComponent class="button-primary" :is-loading="isLoading">
              Enregistrer
            </ButtonComponent>
          </div>
        </div>
      </form>
    </template>
  </DefaultModal>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { onMounted, ref } from 'vue'
import DefaultModal from '@/vuejs/modules/account/pages/DefaultModalPage.vue'
import SavedCartForm from '@/vuejs/modules/account/components/savedCart/SavedCartForm.vue'
import { SavedCart } from '@/vuejs/types/SavedCart'
import { useSavedCartStore } from '@/vuejs/stores/savedCart'

const props = defineProps({
  savedCartId: {
    type: String,
    required: false,
    default: null,
  },
  isEditing: {
    type: Boolean,
    required: false,
    default: false,
  },
  isLoading: {
    type: Boolean,
    required: false,
    default: false,
  },
})
const savedCart = ref<SavedCart>()
const savedCartStore = useSavedCartStore()
const emit = defineEmits(['cancel', 'submitSavedCart'])

onMounted(async () => {
  if (props.savedCartId) {
    savedCart.value = savedCartStore.savedCarts.find(
      (sc) => sc.id === props.savedCartId,
    )
  } else {
    savedCart.value = {
      name: null,
    }
  }
})

const onCancelClick = () => {
  emit('cancel')
}

const onSubmit = async () => {
  await emit('submitSavedCart', {
    savedCart: savedCart.value,
    isEditing: props.isEditing,
  })
}
</script>

<style scoped></style>
