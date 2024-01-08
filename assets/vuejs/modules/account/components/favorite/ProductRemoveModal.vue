<template>
  <FavoriteModal @cancel="onCancelClick">
    <template #title> Supprimer un produit de la liste</template>
    <template #content>
      <div class="px-5">
        <div v-if="favoriteProduct">
          <h4 class="text-sm text-white md:text-base lg:text-lg">
            Voulez-vous vraiment supprimer ce produit:
            <strong>{{ favoriteProduct.upplerProductName }}</strong>
          </h4>
        </div>

        <div class="mt-5 flex justify-end">
          <ButtonComponent
            class="button-primary mr-2"
            type="button"
            @click="onCancelClick"
          >
            Annuler
          </ButtonComponent>
          <ButtonComponent
            class="button-primary"
            :is-loading="isLoading"
            @click="onRemoveProduct"
          >
            Supprimer
          </ButtonComponent>
        </div>
      </div>
    </template>
  </FavoriteModal>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import FavoriteModal from '@/vuejs/modules/account/pages/DefaultModalPage.vue'
import { PropType } from 'vue'
import { FavoriteProduct } from '@/vuejs/types/Favorite'

const props = defineProps({
  favoriteId: {
    type: String,
    required: false,
    default: null,
  },
  favoriteProduct: {
    type: Object as PropType<FavoriteProduct>,
    required: true,
  },
  isLoading: {
    type: Boolean,
    required: false,
    default: false,
  },
})
const emit = defineEmits(['cancel', 'removeProduct'])

const onCancelClick = () => {
  emit('cancel')
}

const onRemoveProduct = async () => {
  await emit('removeProduct', {
    favoriteProductId: props.favoriteProduct.id,
  })
}
</script>

<style scoped></style>
