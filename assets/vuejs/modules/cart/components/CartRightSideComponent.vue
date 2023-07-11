<template>
  <div>
    <div
      class="flex flex-col items-center justify-between rounded-lg bg-white p-5 md:flex-row lg:flex-col lg:p-3 xl:p-5"
    >
      <div class="w-auto md:w-7/12 lg:w-auto">
        <h3 class="mb-5 text-[19px] text-primary xl:text-[25px]">
          <slot name="title" />
        </h3>
        <div
          class="mb-2 inline-flex w-full justify-between text-sm text-gray-500 md:text-base xl:text-lg"
        >
          <div>Sous-total HT :</div>
          <div class="float-right">{{ subTotalWithoutTaxesDisplayed }}€</div>
        </div>
        <div
          class="mb-2 inline-flex w-full justify-between text-sm text-gray-500 md:text-base xl:text-lg"
        >
          <div>Frais de livraison HT :</div>
          <div class="float-right flex items-center">
            <template v-if="showShipmentPrice">
              {{ shipmentPrice }} €
            </template>
            <VTooltip :triggers="['hover', 'focus']" v-else>
              <InformationIconComponent class="text-secondary" />
              <template #popper>
                Le montant des frais de livraison sera indiqué <br />lors de
                l'étape "livraison"
              </template>
            </VTooltip>
          </div>
        </div>
        <div
          class="primary mb-2 inline-flex w-full justify-between text-sm font-bold md:text-base xl:text-lg"
        >
          <div>TOTAL HT :</div>
          <div class="float-right">
            {{
              showShipmentPrice
                ? totalWithoutTaxesDisplayed
                : subTotalWithoutTaxesDisplayed
            }}€
          </div>
        </div>
        <div
          v-if="showShipmentPrice"
          class="inline-flex w-full justify-between text-sm text-gray-500 md:text-base xl:text-lg"
        >
          <div>TOTAL TTC :</div>
          <div class="float-right">{{ totalDisplayed }}€</div>
        </div>
      </div>
      <div class="w-full md:w-4/12 lg:w-auto xl:w-full">
        <slot name="button-next" />
      </div>
    </div>
    <div v-if="hasPaymentMethods" class="mt-5 flex justify-start">
      <div
        v-if="!!CBPaymentMethod"
        class="items-center rounded-lg bg-white p-5"
      >
        <img class="h-14" :src="cbLogosImg" alt="CB Icons" />
      </div>
      <div
        v-if="!!SEPAPaymentMethod"
        class="h-14 items-center rounded-lg bg-white p-5"
      >
        <SepaIconComponent />
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { computed } from 'vue'

import cbLogos from '@/vuejs/assets/img/cb-icons.png'
import SepaIconComponent from '@/vuejs/modules/shared/icon/SepaIconComponent.vue'
import InformationIconComponent from '@/vuejs/modules/shared/icon/InformationIconComponent.vue'

import { formatPrice, getImage } from '@/vuejs/services/utils'
import { useCartStore } from '@/vuejs/stores/cart'

const cartStore = useCartStore()

const { cart, CBPaymentMethod, SEPAPaymentMethod } = storeToRefs(cartStore)

const cbLogosImg = getImage(cbLogos)

const props = defineProps({
  showNextButton: {
    required: false,
    type: Boolean,
    default: true,
  },
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
})

const subTotalWithoutTaxes = computed((): number => {
  let total: number = 0
  cart.value.orders.forEach((o) => {
    total += o.items_total_excluding_taxes
  })
  return total
})

const subTotalWithoutTaxesDisplayed = computed((): string => {
  return formatPrice(subTotalWithoutTaxes.value / 100)
})

const totalWithoutTaxesDisplayed = computed((): string => {
  return formatPrice(cart.value.total_excluding_taxes / 100)
})

const shipmentPrice = computed((): string => {
  return formatPrice(
    (cart.value.total_excluding_taxes - subTotalWithoutTaxes.value) / 100,
  )
})

const totalDisplayed = computed((): string => {
  return formatPrice(cart.value.total / 100)
})
</script>
