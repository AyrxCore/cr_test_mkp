<template>
  <h3 class="text-title-primary mb-2 mt-8">Choisir un type de paiement</h3>
  <div class="flex flex-col-reverse lg:grid lg:grid-cols-4 lg:gap-4 lg:px-0">
    <div class="col-span-3 flex flex-col lg:grid lg:grid-cols-2 lg:gap-2">
      <PaymentMethodComponent
        v-if="CBPaymentMethod"
        :is-loading="isCBLoading"
        :method="CBPaymentMethod"
        class="lg:mr-4"
        @select-method="selectCB"
      >
        <template #method-icon>
          <img :src="cbLogosImg" alt="CB Icons" class="m-auto h-20" />
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
      <PaymentMethodComponent
        v-if="showMandatAdminPayment"
        :is-loading="isMandatAdminLoading"
        :method="mandatAdminPaymentMethod"
        class="lg:mr-4"
        @select-method="selectMandatAdmin"
      >
        <template #method-icon>
          <TownHallIcon class="m-auto" />
        </template>
      </PaymentMethodComponent>
      <template v-if="noMethodAvailable">
        Aucune méthode de paiement disponible
      </template>
    </div>
    <CartRightSideComponent :has-payment-methods="false">
      <template #title>Récapitulatif panier</template>
    </CartRightSideComponent>
  </div>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'
import { useHead } from '@unhead/vue'

import { PageList } from '@/vuejs/router'
import { useCartStore } from '@/vuejs/stores/cart'
import { getImage, notifyError, setHeadTitle } from '@/vuejs/services/utils'
import { formatCartItemsGtmEvent, sendGtmEvent } from '@/vuejs/services/gtm'
import { PaymentMethod } from '@/vuejs/types/Cart'

import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import PaymentMethodComponent from '@/vuejs/modules/cart/components/PaymentMethodComponent.vue'
import SepaIconComponent from '@/vuejs/modules/shared/icon/SepaIconComponent.vue'
import cbLogos from '@/vuejs/assets/img/cb-icons.png'
import TownHallIcon from '@/vuejs/modules/shared/icon/TownHallIconComponent.vue'

const cartStore = useCartStore()
const router = useRouter()

const {
  CBPaymentMethod,
  SEPAPaymentMethods,
  mandatAdminPaymentMethod,
  showMandatAdminPayment,
} = storeToRefs(cartStore)
const isCBLoading = ref<boolean>(false)
const isMandatAdminLoading = ref<boolean>(false)

const cbLogosImg = getImage(cbLogos)

const noMethodAvailable = computed((): boolean => {
  return (
    !CBPaymentMethod.value &&
    SEPAPaymentMethods.value.length === 0 &&
    !showMandatAdminPayment.value
  )
})

const selectCB = async () => {
  isCBLoading.value = true
  const payment = await cartStore.updateCartPaymentMethod(
    CBPaymentMethod.value.id,
  )
  if (payment.payment_url) {
    sendGtmEvent('add_payment_info', {
      ecommerce: {
        currency: 'EUR',
        items: formatCartItemsGtmEvent(cartStore.cart),
      },
    })
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
  sendGtmEvent('add_payment_info', {
    ecommerce: {
      currency: 'EUR',
      items: formatCartItemsGtmEvent(cartStore.cart),
    },
  })
  router.push({ name: PageList.CART_PAYMENT_SEPA })
}

const selectMandatAdmin = async () => {
  isMandatAdminLoading.value = true
  const result = await cartStore.updateCartPaymentMethod(
    mandatAdminPaymentMethod.value.id,
  )
  if (result) {
    sendGtmEvent('add_payment_info', {
      ecommerce: {
        currency: 'EUR',
        items: formatCartItemsGtmEvent(cartStore.cart),
      },
    })
    window.location.replace(
      `${window.location.origin}/api/buyer/cart/${cartStore.cart.id}/confirm`,
    )
  } else {
    isMandatAdminLoading.value = false
  }
}

useHead({
  title: 'Paiement | QANTIS Marketplace',
  meta: [{ property: 'og:title', content: 'Paiement | QANTIS Marketplace' }],
})

onMounted(() => {
  sendGtmEvent('add_shipping_info', {
    ecommerce: {
      currency: 'EUR',
      items: formatCartItemsGtmEvent(cartStore.cart),
    },
  })
})
</script>
