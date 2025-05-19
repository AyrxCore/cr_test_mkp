<template>
  <Modal :close="closeModal">
    <div
      v-if="seller"
      class="mx-4 my-4 rounded bg-white p-4 md:max-h-[800px] md:w-[600px]"
    >
      <div class="mb-4 underline">
        Conditions Générales de Vente de {{ seller.name }}
      </div>
      <div
        v-if="seller.tos?.content"
        class="max-h-[650px] overflow-y-scroll"
        v-html="seller.tos.content"
      />
      <div v-else class="italic">Conditions générales indisponibles</div>
      <div class="mt-4 text-center">
        <ButtonComponent class="button-primary" @click="$emit('validate')">
          Valider
        </ButtonComponent>
      </div>
    </div>
  </Modal>
</template>
<script lang="ts" setup>
import { PropType } from 'vue'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'

import { Seller } from '@/vuejs/types/Seller'

const props = defineProps({
  seller: {
    required: true,
    type: Object as PropType<Seller>,
  },
})

const emit = defineEmits(['validate', 'close'])
const closeModal = () => {
  emit('close')
}
</script>
