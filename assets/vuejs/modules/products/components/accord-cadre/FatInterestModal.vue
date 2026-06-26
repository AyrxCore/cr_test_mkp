<template>
  <Modal v-if="modelValue" :close="closeInterestModal">
    <div class="modal mx-4 my-4 rounded bg-white p-10 md:w-[900px]">
      <div class="flex justify-end">
        <ButtonComponent
          class="!h-5 !px-3 text-xl font-bold !text-primary"
          type="button"
          @click="closeInterestModal"
        >
          <CloseIconComponent />
        </ButtonComponent>
      </div>
      <h3 class="mb-10 text-center text-3xl font-bold text-primary">
        Préciser mon besoin
      </h3>
      <FormFatInterest :accord="accord" @success="closeInterestModal" />
    </div>
  </Modal>
</template>

<script lang="ts" setup>
import { PropType } from 'vue'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CloseIconComponent from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'
import FormFatInterest from '@/vuejs/modules/products/components/accord-cadre/FormFatInterest.vue'

import { Product } from '@/vuejs/types/Product'

defineProps({
  modelValue: {
    required: true,
    type: Boolean,
  },
  accord: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const emit = defineEmits(['close', 'update:modelValue'])

const closeInterestModal = () => {
  emit('update:modelValue', false)
}
</script>

