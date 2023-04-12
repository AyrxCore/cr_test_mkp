<template>
  <div class="mt-2 rounded-lg bg-white p-5 text-center lg:mt-0 lg:mr-4">
    <slot name="method-icon" />
    <ButtonComponent
      :is-loading="isLoading"
      class="button-gradient mt-4 !whitespace-normal"
      @click="chosePaymentMethod"
    >
      Choisir le paiement par {{ method.name.default }}
    </ButtonComponent>
  </div>
</template>
<script lang="ts" setup>
import { PropType, ref } from 'vue'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'

import { PaymentMethod } from '@/vuejs/types/Cart'
import { useCartStore } from '@/vuejs/stores/cart'

const cartStore = useCartStore()

const props = defineProps({
  method: {
    required: true,
    type: Object as PropType<PaymentMethod>,
  },
})

const isLoading = ref<boolean>(false)

const chosePaymentMethod = async () => {
  isLoading.value = true
  const payment = await cartStore.updateCartPaymentMethod(props.method.id)
  if (payment.payment_url) {
    window.location.replace(payment.payment_url)
  } else {
    isLoading.value = false
  }
}
</script>

<style scoped></style>
