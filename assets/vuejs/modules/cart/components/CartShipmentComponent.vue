<template>
  <h2 class="my-4 text-base text-primary md:text-xl lg:mt-0">
    Livraison {{ seller.name }}
    <slot name="order-index" />
  </h2>
  <div class="mb-5 rounded-lg bg-white p-5">
    <p class="mb-4 font-bold text-primary">
      Sélectionnez votre méthode de livraison
    </p>
    <p class="mb-2 font-bold">Méthode(s) de livraison disponible(s) :</p>
    <div v-show="!isLoading">
      <div class="flex items-center pb-2">
        <input
          :id="`shipmentMethod-${cartOrder.seller.name}`"
          class="checked:bg-secondary checked:hover:bg-secondary focus:bg-secondary focus:outline-none focus:ring-1 focus:ring-secondary checked:focus:bg-secondary checked:active:bg-secondary"
          type="radio"
          :checked="isChecked"
          @change="isChecked = true"
        />
        <label :for="`shipmentMethod-${cartOrder.seller.name}`" class="pl-2">
          Frais de port - {{ cartOrder.seller.name }} -
          {{ cartOrder.shippingCostResult.shippingCost }}€
        </label>
      </div>
    </div>
    <LoaderSharedComponent v-show="isLoading" class="my-2" />
    <hr class="mt-2" />
    <div
      class="mt-4 flex cursor-pointer"
      @click="isDetailsOpen = !isDetailsOpen"
    >
      {{ realProducts.length }} référence(s)
      <span class="ml-2 flex items-center font-bold text-secondary underline">
        <Chevron2RightIconComponent
          :class="{
            'rotate-90 ease-in-out': isDetailsOpen,
          }"
          class="mr-1 fill-secondary stroke-secondary text-sm lg:text-lg"
        />
        Détails
      </span>
    </div>
    <table v-if="isDetailsOpen" class="mt-4 w-full">
      <thead class="text-left font-bold">
        <tr>
          <th class="pb-2">Description de l'article</th>
          <th class="pb-2">Quantité</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(item, key) in realProducts"
          :key="key"
          class="odd:bg-gray-50 even:bg-gray-100"
        >
          <td class="px-4 py-2">
            <span class="text-primary">{{ item.name }}</span
            ><br />
            <span class="text-sm"> Référence : {{ item.sku }} </span>
          </td>
          <td>{{ item.quantity }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script lang="ts" setup>
import { computed, onMounted, PropType, ref } from 'vue'

import { useSellerStore } from '@/vuejs/stores/seller'
import { CartOrder } from '@/vuejs/types/Cart'
import { Seller } from '@/vuejs/types/Seller'
import { PRODUCT_FDP_PREFIX } from '@/vuejs/services/const.ts'

import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'

const sellerStore = useSellerStore()

const emit = defineEmits(['loaded'])

const props = defineProps({
  cartOrder: {
    required: true,
    type: Object as PropType<CartOrder>,
  },
})

const isLoading = ref<boolean>(false)
const isDetailsOpen = ref<boolean>(false)
const isChecked = ref<boolean>(true)

onMounted(async (): Promise<void> => {
  emit('loaded', false)
  isLoading.value = true
  const sellerId = props.cartOrder.seller.id
  await sellerStore.getSeller(sellerId)
  isLoading.value = false
  emit('loaded', true)
})

const seller = computed((): Seller => {
  return sellerStore.allSellers.find((e) => e.id === props.cartOrder.seller.id)
})

const realProducts = computed(() => {
  return props.cartOrder.products.filter(
    (p) => !p.externalId?.startsWith(PRODUCT_FDP_PREFIX),
  )
})
</script>
