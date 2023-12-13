<template>
  <div
    class="mt-2 flex flex-col rounded-lg bg-white p-5 text-center lg:mt-0 lg:mr-4"
  >
    <slot name="method-icon" />
    <ButtonComponent
      :is-loading="isLoading"
      class="button button-gradient mt-4 !whitespace-normal"
      @click="emit('selectMethod')"
    >
      Choisir le paiement par {{ method.name.default }}
    </ButtonComponent>
  </div>
</template>
<script lang="ts" setup>
import { PropType, ref } from 'vue'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'

import { PaymentMethod } from '@/vuejs/types/Cart'

const props = defineProps({
  method: {
    required: true,
    type: Object as PropType<PaymentMethod>,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits<{
  (e: 'selectMethod'): void
}>()
</script>

<style scoped></style>
