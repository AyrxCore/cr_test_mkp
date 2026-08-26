<template>
  <div>
    <div
      class="flex flex-col items-center justify-between rounded-lg bg-white p-4 md:flex-row lg:flex-col lg:px-6 lg:py-4"
    >
      <div class="w-full">
        <h3 class="mb-2 text-[19px] text-primary lg:mb-4 xl:text-[25px]">
          <slot name="title" />
        </h3>
        <div
          class="mb-2 inline-flex w-full justify-between text-sm md:text-base xl:text-lg"
        >
          <div>Sous-total HT :</div>
          <div class="float-right">{{ subTotalWithoutTaxesDisplayed }}€</div>
        </div>
        <div
          class="mb-2 inline-flex w-full justify-between text-sm md:text-base xl:text-lg"
        >
          <div>Frais de livraison HT :</div>
          <div class="float-right flex items-center">
            <template v-if="showShipmentPrice">
              {{ shipmentPriceDisplayed }} €
            </template>
            <VTooltip v-else :triggers="['hover', 'focus']">
              <InformationIconComponent class="text-primary" />
              <template #popper>
                Le montant des frais de livraison sera indiqué <br />lors de
                l'étape "livraison"
              </template>
            </VTooltip>
          </div>
        </div>
        <div
          class="inline-flex w-full justify-between text-sm font-bold text-primary md:text-base xl:text-lg"
        >
          <div>TOTAL HT :</div>
          <div class="float-right">
            {{ showShipmentPrice ? totalWithoutTaxesDisplayed : subTotalWithoutTaxesDisplayed }}€
          </div>
        </div>
        <div
          v-if="ecoTaxTotalDisplayed"
          class="mb-2 inline-flex w-full justify-end"
        >
          <span class="rounded py-0.5 text-xs text-gray-700">
            dont {{ ecoTaxTotalDisplayed }}€ d'éco-part
          </span>
        </div>
        <div
          v-if="showShipmentPrice"
          class="mb-2 inline-flex w-full justify-between text-sm md:text-base xl:text-lg"
        >
          <div>TOTAL TTC :</div>
          <div class="float-right">{{ grandTotalDisplayed }}€</div>
        </div>
      </div>
      <div class="w-full md:pl-8 lg:px-0">
        <slot name="button-next" />
      </div>
    </div>
    <div
      v-if="showPaymentMethods && CBPaymentMethod"
      class="flex flex-wrap gap-2"
    >
      <div class="mt-5 flex flex-wrap gap-2">
        <div class="rounded-lg bg-white p-2">
          <img :src="cbPaymentImage" alt="CB Icons" class="max-h-8" />
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'

import { useCartStore } from '@/vuejs/stores/cart'
import { PRODUCT_FDP_PREFIX } from '@/vuejs/services/const'
import { formatPrice, getImage } from '@/vuejs/services/utils'

import InformationIconComponent from '@/vuejs/modules/shared/icon/InformationIconComponent.vue'
import cbPaymentAsset from '@/vuejs/assets/img/payments/payment_cb.png'

const cartStore = useCartStore()

const { cart, CBPaymentMethod, shippingCostTotal, shippingCostTotalWithTax } =
  storeToRefs(cartStore)

const cbPaymentImage = getImage(cbPaymentAsset)

defineProps({
  hasPaymentMethods: {
    required: false,
    type: Boolean,
    default: true,
  },
  showShipmentPrice: {
    required: false,
    type: Boolean,
    default: true,
  },
  showPaymentMethods: {
    required: false,
    type: Boolean,
    default: false,
  },
})

const subTotalWithoutTaxes = computed((): number => {
  let total: number = 0
  cart.value.cartOrders.forEach((ol) => {
    ol.products.forEach((p) => {
      if (!p.externalId?.startsWith(PRODUCT_FDP_PREFIX)) {
        total += (p.price ?? 0) * (p.quantity ?? 0)
      }
    })
  })
  return total
})

const subTotalWithoutTaxesDisplayed = computed((): string => {
  return formatPrice(subTotalWithoutTaxes.value)
})

const shipmentPriceDisplayed = computed((): string => {
  return formatPrice(shippingCostTotal.value)
})

const totalWithoutTaxesDisplayed = computed((): string => {
  return formatPrice(subTotalWithoutTaxes.value + shippingCostTotal.value)
})


const grandTotalDisplayed = computed((): string => {
  return formatPrice(cart.value.totalPriceWithTax + shippingCostTotalWithTax.value)
})

const ecoTaxTotalDisplayed = computed((): string | null => {
  let total = 0
  cart.value?.cartOrders?.forEach((ol) => {
    ol.products?.forEach((p) => {
      if (!p.externalId?.startsWith(PRODUCT_FDP_PREFIX) && p.ecoTax) {
        total += p.ecoTax * (p.quantity ?? 1)
      }
    })
  })
  return total > 0 ? formatPrice(total) : null
})
</script>
