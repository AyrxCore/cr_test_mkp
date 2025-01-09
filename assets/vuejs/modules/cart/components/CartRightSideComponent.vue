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
              {{ shipmentPrice }} €
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
          class="mb-2 inline-flex w-full justify-between text-sm font-bold text-primary md:text-base xl:text-lg"
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
          class="inline-flex w-full justify-between text-sm md:text-base xl:text-lg"
        >
          <div>TOTAL TTC :</div>
          <div class="float-right">{{ totalDisplayed }}€</div>
        </div>
      </div>
      <div class="w-full md:pl-8 lg:px-0">
        <slot name="button-next" />
      </div>
    </div>
    <div v-if="hasPaymentMethods" class="mt-5 flex flex-wrap gap-2">
      <div v-if="!!CBPaymentMethod" class="rounded-lg bg-white p-2">
        <img :src="cbPaymentImage" alt="CB Icons" class="max-h-8" />
      </div>
      <div v-if="SEPAPaymentMethods.length > 0" class="rounded-lg bg-white p-2">
        <img :src="sepaPaymentImage" alt="SEPA Icon" class="max-h-8" />
      </div>
      <div v-if="showMandatAdminPayment" class="rounded-lg bg-white p-2">
        <img
          :src="mandatePaymentImage"
          alt="Mandat administratif Icon"
          class="max-h-8"
        />
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { computed } from 'vue'

import cbPaymentAsset from '@/vuejs/assets/img/payments/payment_cb.png'
import sepaPaymentAsset from '@/vuejs/assets/img/payments/payment_sepa.png'
import mandatePaymentAsset from '@/vuejs/assets/img/payments/payment_mandate.png'
import InformationIconComponent from '@/vuejs/modules/shared/icon/InformationIconComponent.vue'

import { formatPrice, getImage } from '@/vuejs/services/utils'
import { useCartStore } from '@/vuejs/stores/cart'

const cartStore = useCartStore()

const { cart, CBPaymentMethod, SEPAPaymentMethods, showMandatAdminPayment } =
  storeToRefs(cartStore)

const cbPaymentImage = getImage(cbPaymentAsset)
const sepaPaymentImage = getImage(sepaPaymentAsset)
const mandatePaymentImage = getImage(mandatePaymentAsset)

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
