<template>
  <FavoriteModal>
    <template #title> Supprimer un produit de la liste</template>
    <template #content>
      <div class="px-5">
        <div v-if="product">
          <h4 class="text-sm text-white md:text-base lg:text-lg">
            Voulez-vous vraiment supprimer ce produit:
            <strong>{{ product.name }}</strong>
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
            @click="onRemoveItem"
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
import FavoriteModal from '@/vuejs/modules/account/pages/FavoriteModalPage.vue'

const props = defineProps({
  favoriteId: {
    type: String,
    required: false,
    default: null,
  },
  product: {
    type: Object,
    required: true,
  },
  isLoading: {
    type: Boolean,
    required: false,
    default: false,
  },
})
const emit = defineEmits(['cancel', 'removeItem'])

const onCancelClick = () => {
  emit('cancel')
}

const onRemoveItem = async () => {
  await emit('removeItem', {
    favoriteId: props.favoriteId,
    productId: props.product.id,
  })
}
</script>

<style scoped></style>
