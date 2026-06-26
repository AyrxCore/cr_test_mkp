<template>
  <div class="bg-white py-5">
    <div class="px-5">
      <h3
        class="mb-4 flex items-center justify-between text-[19px] text-primary md:justify-start md:text-[25px]"
      >
        <span>{{ seller?.name || '-' }}</span>
        <span class="ml-2 text-sm font-bold text-black">
          {{ products.length }} référence(s)
        </span>
      </h3>
      <div class="hidden justify-between text-sm lg:flex lg:w-full">
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
      v-for="(product, key) in products"
      :key="key"
      :product="product"
      class="border-b border-gray-200"
      @is-loading-prices="isLoadingPrices = $event"
    />
    <div class="px-5">
      <div class="mt-2 flex w-full flex-row justify-center lg:justify-start">
        <div class="hidden lg:flex lg:w-7/12"></div>
        <div class="flex w-full justify-between lg:w-5/12">
          <div class="hidden lg:flex lg:w-2/12"></div>
          <div class="w-4/12 text-center lg:w-4/12">
            <span class="text-sm">Sous-total fournisseur</span>
          </div>
          <div class="w-5/12 lg:w-4/12">
            <SkeletonLoading v-if="isLoadingPrices" class="flex justify-center" />
            <div v-else class="flex flex-col items-center lg:items-center">
              <span class="text-lg font-bold text-primary">
                {{ totalPriceDisplayed }}
                € HT
              </span>
              <span
                v-if="ecoTaxOrderTotal"
                class="mt-0.5 rounded py-0.5 text-xs text-gray-700"
              >
                dont {{ ecoTaxOrderTotal }}€ d'éco-part
              </span>
            </div>
          </div>
          <div class="hidden lg:flex lg:w-1/12"></div>
        </div>
      </div>
      <div class="flex w-full flex-col">
        <CartFrancoComponent
          :cart-order="cartOrder"
          :is-loading="isLoadingPrices"
          class="mt-5"
        />
        <p class="mt-7 flex items-center text-sm md:items-center lg:text-lg">
          <input
            v-model="orderTermsOfSales"
            class="mr-2 cursor-pointer lg:mt-0"
            type="checkbox"
            @change="onTermsChange"
          />
          <span>
            J'accepte les
            <span class="ml-1 cursor-pointer underline" @click="showTos = true">
              Conditions Générales de Vente du fournisseur
            </span>
          </span>

          <TosOrderComponent
            v-if="showTos"
            :seller="seller"
            @close="showTos = false"
            @validate="validateCgu"
          />
        </p>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed, onMounted, PropType, ref } from 'vue'

import { useCartStore } from '@/vuejs/stores/cart'
import { useSellerStore } from '@/vuejs/stores/seller'
import { formatPrice } from '@/vuejs/services/utils'
import { Seller } from '@/vuejs/types/Seller'
import { CartOrder } from '@/vuejs/types/Cart'
import { Product } from '@/vuejs/types/Product.ts'
import { PRODUCT_FDP_PREFIX } from '@/vuejs/services/const.ts'

import CartFrancoComponent from '@/vuejs/modules/cart/components/CartFrancoComponent.vue'
import ProductRecapComponent from '@/vuejs/modules/cart/components/ProductRecapComponent.vue'
import TosOrderComponent from '@/vuejs/modules/cart/components/TosOrderComponent.vue'
import SkeletonLoading from '@/vuejs/modules/shared/SkeletonLoading.vue'

const cartStore = useCartStore()
const sellerStore = useSellerStore()

const props = defineProps({
  cartOrder: {
    required: true,
    type: Object as PropType<CartOrder>,
  },
})

const orderTermsOfSales = ref<boolean>(false)
const showTos = ref<boolean>(false)
const isLoadingPrices = ref<boolean>(false)

const seller = computed((): Seller => {
  return sellerStore.allSellers.find((e) => e.id === props.cartOrder.seller.id)
})

onMounted(async (): Promise<void> => {
  const sellerId = props.cartOrder.seller.id
  await sellerStore.getSeller(sellerId)
})

const products = computed((): Product[] => {
  return [...props.cartOrder.products]
    .filter((p) => !p.externalId?.startsWith(PRODUCT_FDP_PREFIX))
    .sort((a, b) => a.name.localeCompare(b.name))
})

const totalPriceDisplayed = computed((): string => {
  return formatPrice(props.cartOrder.totalPrice)
})

const ecoTaxOrderTotal = computed((): string | null => {
  const total = products.value.reduce((sum, p) => {
    const unitEcoTax = p.ecoTax ?? 0
    return sum + unitEcoTax * (p.quantity ?? 1)
  }, 0)
  return total > 0 ? formatPrice(total) : null
})

const onTermsChange = (): void => {
  cartStore.termsOfSales = cartStore.termsOfSales.filter(
    (e) => e !== props.cartOrder.seller.id,
  )
  if (orderTermsOfSales.value) {
    cartStore.termsOfSales.push(props.cartOrder.seller.id)
  }
}

const validateCgu = (): void => {
  orderTermsOfSales.value = true
  onTermsChange()
  showTos.value = false
}
</script>
