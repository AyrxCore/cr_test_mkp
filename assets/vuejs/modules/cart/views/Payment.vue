<template>
  <h3 class="text-title-primary mb-2 mt-8">Choisir un type de paiement</h3>
  <div class="flex flex-col-reverse lg:grid lg:grid-cols-4 lg:gap-4 lg:px-0">
    <div class="col-span-3 flex flex-col lg:grid lg:grid-cols-2 lg:gap-2">
      <PaymentMethodComponent
        v-if="CBPaymentMethod"
        :method="CBPaymentMethod"
        :is-loading="isCBLoading"
        class="lg:mr-4"
        @select-method="selectCB"
      >
        <template #method-icon>
          <img class="m-auto h-20" :src="cbLogosImg" alt="CB Icons" />
        </template>
      </PaymentMethodComponent>
      <PaymentMethodComponent
        v-for="method in SEPAPaymentMethods"
        :method="method"
        @select-method="selectSEPA(method)"
      >
        <template #method-icon>
          <SepaIconComponent class="m-auto" />
        </template>
      </PaymentMethodComponent>
      <template v-if="!CBPaymentMethod && SEPAPaymentMethods.length === 0">
        Aucune méthode de paiement disponible
      </template>
    </div>
    <CartRightSideComponent :has-payment-methods="false">
      <template #title>Récapitulatif panier</template>
    </CartRightSideComponent>
  </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'

import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import PaymentMethodComponent from '@/vuejs/modules/cart/components/PaymentMethodComponent.vue'
import SepaIconComponent from '@/vuejs/modules/shared/icon/SepaIconComponent.vue'
import cbLogos from '@/vuejs/assets/img/cb-icons.png'

import { getImage, notifyError } from '@/vuejs/services/utils'
import { useCartStore } from '@/vuejs/stores/cart'
import { PageList } from '@/vuejs/router'
import { PaymentMethod } from '@/vuejs/types/Cart'

const cartStore = useCartStore()
const router = useRouter()

const { CBPaymentMethod, SEPAPaymentMethods } = storeToRefs(cartStore)
const isCBLoading = ref<boolean>(false)

const cbLogosImg = getImage(cbLogos)

const selectCB = async () => {
  isCBLoading.value = true
  const payment = await cartStore.updateCartPaymentMethod(
    CBPaymentMethod.value.id,
  )
  if (payment.payment_url) {
    window.location.replace(payment.payment_url)
  } else {
    notifyError(
      'Le paiement est impossible, merci de contacter un administrateur.',
    )
    isCBLoading.value = false
  }
}

const selectSEPA = async (method: PaymentMethod) => {
  cartStore.selectedSepa = method
  router.push({
    name: PageList.CART_PAYMENT_SEPA,
  })
}
</script>

<style scoped></style>
