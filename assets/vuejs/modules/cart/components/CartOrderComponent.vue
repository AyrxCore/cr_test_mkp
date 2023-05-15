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
        <div class="text-sm italic text-gray-500 lg:text-base">
          <p v-if="notDisplayedPromotion" class="mt-5">
            {{ seller?.description }}
          </p>
          <p v-else-if="leftBeforePromotion" class="mt-5">
            Il vous reste {{ leftBeforePromotion }}€ HT de commande pour
            bénéficier de {{ promotionType }}
          </p>
          <p v-else-if="hasReachedFranco" class="mt-5 text-green-600">
            Franco atteint - vous bénéficiez de la livraison gratuite
          </p>
        </div>
        <p
          class="mt-5 flex flex-col text-sm text-gray-500 lg:mt-7 lg:flex-row lg:items-center lg:text-lg"
        >
          Méthode de livraison :
          <select
            v-if="order.shippingMethodsAvailable.length > 0"
            v-model="selectedShippingMethod"
            class="flex h-[35px] rounded-md py-0 text-gray-600 placeholder-gray-400 lg:ml-2"
            @change="selectshippingMethod"
          >
            <option
              v-for="method in order.shippingMethodsAvailable"
              :value="method.shipping_method.id"
            >
              {{ method.shipping_method.name.fr }} - {{ method.amount / 100 }}€
            </option>
          </select>
          <template v-else>Aucune méthode de livraison disponible</template>
        </p>
        <p
          class="mt-7 inline-flex items-start text-sm text-gray-500 lg:items-center lg:text-lg"
        >
          <input
            v-model="termsOfSales"
            type="checkbox"
            class="mr-2 mt-1 cursor-pointer lg:mt-0"
            @change="onTermsChange"
          />
          J'accepte les&nbsp;
          <span class="cursor-pointer underline" @click="showTos = true">
            Conditions Générales de Vente du fournisseur
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
import { PropType, computed, ref, onMounted } from 'vue'
import { Order } from '@/vuejs/types/Cart'

import { formatPrice } from '@/vuejs/services/utils'

import TosOrderComponent from '@/vuejs/modules/cart/components/TosOrderComponent.vue'
import ProductRecapComponent from '@/vuejs/modules/cart/components/ProductRecapComponent.vue'
import { useCartStore } from '@/vuejs/stores/cart'
import { useSellerStore } from '@/vuejs/stores/seller'
import { Seller, SellerPromotion } from '@/vuejs/types/Seller'

const cartStore = useCartStore()
const sellerStore = useSellerStore()

const props = defineProps({
  order: {
    required: true,
    type: Object as PropType<Order>,
  },
})

const termsOfSales = ref<boolean>(false)
const showTos = ref<boolean>(false)

const selectedShippingMethod = ref<number>(
  props.order.shippingMethodsAvailable.find((e) => e.selected)?.shipping_method
    ?.id || 0,
)

const seller = ref<Seller>()
const promotions = ref<SellerPromotion[]>([])

onMounted(async (): Promise<void> => {
  const sellerId = props.order.seller.id
  let [sellerVal, promotionsVal] = await Promise.all([
    sellerStore.getSeller(sellerId),
    sellerStore.getSellerPromotions(sellerId),
  ])
  seller.value = sellerVal
  promotions.value = promotionsVal
})

const totalPriceDisplayed = computed((): string => {
  return formatPrice(props.order.total_excluding_taxes / 100)
})

const notDisplayedPromotion = computed((): boolean => {
  const SELLERS_NO_DISPLAY_PROMOTION = [
    26, // KRÖMM
  ]
  return SELLERS_NO_DISPLAY_PROMOTION.includes(seller.value?.id)
})

const leftBeforePromotion = computed((): string => {
  if (!nextPromotion.value) return null
  return formatPrice(
    (nextPromotion.value.order_eligibility.amount -
      props.order.items_total_excluding_taxes) /
      100,
  )
})

const nextPromotion = computed((): SellerPromotion => {
  const total = props.order.items_total_excluding_taxes
  let currentPromotion: SellerPromotion = null
  if (!promotions.value.length) return
  promotions.value.forEach((p, id) => {
    if (!currentPromotion && total < p.order_eligibility.amount) {
      currentPromotion = p
    } else if (
      total < p.order_eligibility.amount &&
      currentPromotion.order_eligibility.amount > p.order_eligibility.amount
    ) {
      currentPromotion = p
    }
  })

  return currentPromotion
})

const hasReachedFranco = computed((): boolean => {
  return (
    promotions.value &&
    promotions.value.length > 0 &&
    nextPromotion.value === null
  )
})

const promotionType = computed((): string => {
  const condition = nextPromotion.value?.conditions[0]
  if (!condition) return
  if (condition.apply_type === 'percent') {
    if (condition.apply_value === 1.0) {
      return 'la livraison gratuite'
    } else {
      return `${condition.apply_value * 100}% de réduction sur la livraison `
    }
  } else if (condition.apply_type === 'amount') {
    return `${condition.apply_value / 100}€ de réduction sur la livraison `
  }
})

const selectshippingMethod = async (): Promise<void> => {
  await cartStore.updateOrderShipping({
    cartId: cartStore.cart.id,
    orderId: props.order.id,
    shippingId: selectedShippingMethod.value,
  })
  cartStore.getCart()
}

const onTermsChange = (): void => {
  cartStore.termsOfSales = cartStore.termsOfSales.filter(
    (e) => e !== props.order.id,
  )
  if (termsOfSales.value) {
    cartStore.termsOfSales.push(props.order.id)
  }
}
</script>

<style scoped></style>
