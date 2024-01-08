<template>
  <div class="modal-overlay">
    <div
      class="z-9999 fixed mx-3 w-full rounded p-3 md:w-[60%] md:p-5 lg:top-20 lg:mx-0"
      :class="background"
    >
      <div class="w-full">
        <div class="flex justify-end">
          <ButtonComponent
            class="!h-5 !px-3 text-xl font-bold !text-primary"
            type="button"
            @click="onCancelClick"
          >
            X
          </ButtonComponent>
        </div>
        <h3 class="text-title-primary mb-3 flex flex-col px-5 !text-white">
          <slot name="title" />
        </h3>
        <slot name="content" />
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'

defineProps({
  background: {
    type: String,
    default: 'bg-secondary',
  },
})
const emit = defineEmits(['cancel'])
const onCancelClick = () => {
  emit('cancel')
}
</script>

<style scoped>
.modal {
  z-index: 999;
}
</style>
