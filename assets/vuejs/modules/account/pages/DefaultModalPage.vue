<template>
  <div class="modal-overlay">
    <div
      class="z-9999 fixed top-20 mx-3 rounded bg-secondary p-3 md:p-5 lg:mx-0"
    >
      <div class="w-full">
        <div class="flex justify-end">
          <ButtonComponent
            class="!h-5 !px-3 text-xl font-bold !text-white"
            type="button"
            @click="onCancelClick"
          >
            X
          </ButtonComponent>
        </div>
        <h3 class="home-title mb-3 flex flex-col px-5 text-white">
          <slot name="title" />
        </h3>
        <slot name="content" />
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'

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
