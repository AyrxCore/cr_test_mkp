<template>
  <div class="bg-white py-5">
    <div class="px-5">
      <h3
        class="mb-4 flex items-center justify-between text-[19px] text-primary md:justify-start md:text-[25px]"
      >
        <span>{{ seller?.name || '-' }}</span>
        <span class="ml-2 text-sm font-bold text-gray-500">
          {{ order.items.length }} référence(s)
        </span>
      </h3>
      <div
        class="hidden justify-between text-sm text-gray-400 lg:flex lg:w-full"
      >
        <div class="flex lg:w-7/12">Description de l'article</div>
        <div class="flex justify-between lg:w-5/12">
          <div class="w-2/12 text-center">Qté</div>
          <div class="w-4/12 text-center">Prix public</div>
          <div class="w-4/12 text-center">Sous-total HT</div>
          <div class="w-1/12"></div>
        </div>
      </div>
    </div>
    <ProductRecapComponent
      v-for="item in order.items"
      :key="item.id"
      :item="item"
      class="border-b border-gray-200"
    />
    <div class="px-5">
      <div class="flex w-full flex-row justify-center lg:justify-end">
        <div class="hidden lg:ml-2 lg:flex lg:w-8/12"></div>
        <div class="w-4/12 text-center lg:w-2/12">
          <span class="mt-2 text-sm text-gray-400">
            Sous-total fournisseur
          </span>
        </div>
        <div class="w-5/12 text-left lg:w-2/12 lg:text-center">
          <span class="mt-2 text-lg font-bold text-primary">
            {{ totalPriceDisplayed }}
            € HT
          </span>
        </div>
        <div class="hidden lg:flex lg:w-1/12"></div>
      </div>
      <div class="flex w-full flex-col">
        <CartFrancoComponent :order="order" class="mt-5" />
        <p
          class="mt-7 flex items-center text-sm text-gray-500 md:items-center lg:text-lg"
        >
          <input
            v-model="orderTermsOfSales"
            type="checkbox"
            class="mr-2 cursor-pointer lg:mt-0"
            @change="onTermsChange"
          />
          <span>
            J'accepte les
            <span class="ml-1 cursor-pointer underline" @click="showTos = true">
              Conditions Générales de Vente du fournisseur
            </span>
          </span>

          <TosOrderComponent
            :seller="seller"
            v-if="showTos"
            @close="showTos = false"
          />
        </p>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed, onMounted, PropType, ref } from 'vue'
import { Order } from '@/vuejs/types/Cart'

import { formatPrice } from '@/vuejs/services/utils'

import CartFrancoComponent from '@/vuejs/modules/cart/components/CartFrancoComponent.vue'
import ProductRecapComponent from '@/vuejs/modules/cart/components/ProductRecapComponent.vue'
import TosOrderComponent from '@/vuejs/modules/cart/components/TosOrderComponent.vue'

import { useCartStore } from '@/vuejs/stores/cart'
import { useSellerStore } from '@/vuejs/stores/seller'
import { Seller } from '@/vuejs/types/Seller'

const cartStore = useCartStore()
const sellerStore = useSellerStore()

const props = defineProps({
  order: {
    required: true,
    type: Object as PropType<Order>,
  },
})

const orderTermsOfSales = ref<boolean>(false)
const showTos = ref<boolean>(false)

const seller = computed((): Seller => {
  return sellerStore.sellers.find((e) => e.id === props.order.seller.id)
})

onMounted(async (): Promise<void> => {
  const sellerId = props.order.seller.id
  await sellerStore.getSeller(sellerId)
})

const totalPriceDisplayed = computed((): string => {
  return formatPrice(props.order.items_total_excluding_taxes / 100)
})

const onTermsChange = (): void => {
  cartStore.termsOfSales = cartStore.termsOfSales.filter(
    (e) => e !== props.order.seller.id,
  )
  if (orderTermsOfSales.value) {
    cartStore.termsOfSales.push(props.order.seller.id)
  }
}
</script>

<style scoped></style>
