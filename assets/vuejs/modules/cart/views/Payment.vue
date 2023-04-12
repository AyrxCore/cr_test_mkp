<template>
  <h3 class="mt-10 mb-2 text-title-35 text-primary">
    Choisir un type de paiement
  </h3>
  <div class="flex flex-col-reverse lg:grid lg:grid-cols-4 lg:gap-4 lg:px-0">
    <div class="col-span-3 flex flex-col lg:grid lg:grid-cols-2 lg:gap-2">
      <PaymentMethodComponent
        v-if="CBPaymentMethod"
        :method="CBPaymentMethod"
        class="lg:mr-4"
      >
        <template #method-icon>
          <img class="m-auto h-20" :src="cbLogosImg" alt="CB Icons" />
        </template>
      </PaymentMethodComponent>
      <!-- <PaymentMethodComponent
        v-if="SEPAPaymentMethod"
        :method="SEPAPaymentMethod"
      >
        <template #method-icon>
          <SepaIconComponent class="m-auto" />
        </template>
      </PaymentMethodComponent> -->
      <template v-if="!CBPaymentMethod">
        Aucune méthode de paiement disponible
      </template>
    </div>
    <CartRightSideComponent :has-payment-methods="false">
      <template #title>Récapitulatif</template>
    </CartRightSideComponent>
  </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue'
import { storeToRefs } from 'pinia'

import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import PaymentMethodComponent from '@/vuejs/modules/cart/components/PaymentMethodComponent.vue'
import SepaIconComponent from '@/vuejs/modules/shared/icon/SepaIconComponent.vue'
import cbLogos from '@/vuejs/assets/img/cb-icons.png'

import { formatPrice, getImage } from '@/vuejs/services/utils'
import { useCartStore } from '@/vuejs/stores/cart'

const cartStore = useCartStore()

const { CBPaymentMethod, SEPAPaymentMethod } = storeToRefs(cartStore)

const cbLogosImg = getImage(cbLogos)
</script>

<style scoped></style>
