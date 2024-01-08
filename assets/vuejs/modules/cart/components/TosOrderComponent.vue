<template>
  <Modal>
    <div
      v-if="seller"
      class="my-4 mx-4 overflow-scroll rounded bg-white p-4 md:max-h-[800px] md:w-[600px]"
    >
      <div class="mb-4 underline">
        Conditions Générales de Vente de {{ seller.name }}
      </div>
      <div v-if="seller.tos?.content" v-html="seller.tos.content" />
      <div v-else class="italic">Conditions générales indisponibles</div>
      <div class="mt-4 text-center">
        <ButtonComponent class="button-primary" @click="$emit('close')">
          Fermer
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

const emit = defineEmits<{
  (eventName: 'close'): void
}>()
</script>

<style scoped></style>
