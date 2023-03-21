<template>
  <Modal>
    <div
      class="max-h-96 min-w-[600px] max-w-[300px] overflow-scroll rounded bg-white p-4"
    >
      <template v-if="seller">
        <div class="mb-4 underline">
          Conditions Générales de Vente de {{ seller.name }}
        </div>
        <div v-if="seller.tos?.content" v-html="seller.tos.content" />
        <div v-else class="italic">Conditions générales indisponibles</div>
        <div class="mt-4 text-center">
          <ButtonComponent class="button-gradient" @click="$emit('close')">
            Fermer
          </ButtonComponent>
        </div>
      </template>
      <LoaderSharedComponent
        v-else
        class="loader-xl m-auto my-4 text-primary"
      />
    </div>
  </Modal>
</template>
<script lang="ts" setup>
import { ref, onMounted } from 'vue'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

import { useSellerStore } from '@/vuejs/stores/seller'

const sellerStore = useSellerStore()

const props = defineProps({
  sellerId: {
    required: true,
    type: Number,
  },
})

const emit = defineEmits<{
  (eventName: 'close'): void
}>()

const seller = ref()
const isShow = ref<boolean>(true)

onMounted(async (): Promise<void> => {
  seller.value = await sellerStore.getSeller(props.sellerId)
})
</script>

<style scoped></style>
