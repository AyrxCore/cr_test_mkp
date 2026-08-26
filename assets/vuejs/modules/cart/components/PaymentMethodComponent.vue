<template>
  <div
    class="mt-2 flex flex-col rounded-lg bg-white p-5 text-center lg:mt-0 lg:mr-4"
  >
    <slot name="method-icon" />
    <ButtonComponent
      :disabled="isNeoAutoLogin"
      :is-loading="isLoading"
      class="button-primary mt-4 whitespace-normal!"
      @click="emit('select-method')"
    >
      Choisir le paiement par {{ methodLabel }}
    </ButtonComponent>
  </div>
</template>
<script lang="ts" setup>
import { PropType, computed } from 'vue'
import { storeToRefs } from 'pinia'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { AdyenPaymentMethod, AdyenPaymentMethodType, ADYEN_PAYMENT_TYPE_LABELS } from '@/vuejs/types/Cart'
import { useUserStore } from '@/vuejs/stores/user'

const props = defineProps({
  method: {
    required: true,
    type: Object as PropType<AdyenPaymentMethod>,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
})

const methodLabel = computed(
  () => ADYEN_PAYMENT_TYPE_LABELS[props.method.type as AdyenPaymentMethodType] ?? props.method.name,
)

const { isNeoAutoLogin } = storeToRefs(useUserStore())

const emit = defineEmits<{
  (e: 'select-method'): void
}>()
</script>

<style scoped></style>
