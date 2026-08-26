<template>
  <div class="modal-overlay">
    <div
      class="z-9999 fixed top-[45%] left-[45%] ml-[-150px] flex w-[500px] flex-col items-center justify-center rounded-xl bg-secondary px-2 py-3 text-center text-white opacity-100!"
    >
      <div class="relative mb-3 flex w-full justify-end">
        <button class="right-2 text-lg text-white" @click="emit('cancel')">
          X
        </button>
      </div>
      <h3 class="text-base md:text-lg lg:text-2xl">Attention</h3>
      <div class="pt-3 pb-6 text-sm md:text-base lg:text-lg">
        Votre demande a bien été prise en compte, merci de consulter vos emails
      </div>
      <div
        class="flex items-center space-x-2 rounded-b border-t border-gray-200 p-6 dark:border-gray-600"
      >
        <ButtonComponent
          data-modal-hide="defaultModal"
          type="button"
          class="button-primary"
          @click="emit('validate')"
        >
          Valider
        </ButtonComponent>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'

const emit = defineEmits(['cancel', 'validate'])
</script>

<style scoped>
.modal {
  z-index: 999;
}

.modal-overlay {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  justify-content: center;
  background-color: #000000da;
}
</style>
