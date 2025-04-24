<template>
  <div
    class="rounded-xs my-12 w-fit bg-secondary p-3 text-center text-xl font-bold text-white"
  >
    -{{ product.percent }}% sur le tarif public
  </div>
  <div class="mb-6">
    <h4 class="text-xl font-bold">Comment bénéficier de l'offre ?</h4>
    <span class="text-lg">
      Veuillez cliquer sur le bouton "Je suis intéressé" ci-dessous et nous vous
      recontacterons
    </span>
  </div>
  <ButtonComponent
    class="button-primary md:mt-0"
    @click="showInterestModal = true"
  >
    <ArrowRightIconComponent class="ml-2 w-4" />
    Je suis intéressé
  </ButtonComponent>
  <NotSellableInterestModal v-model="showInterestModal" :product="product" />
</template>

<script lang="ts" setup>
import { PropType, ref } from 'vue'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import NotSellableInterestModal from '@/vuejs/modules/products/components/NotSellableInterestModal.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

import { Product } from '@/vuejs/types/Product'

const showInterestModal = ref<boolean>(false)

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})
</script>
