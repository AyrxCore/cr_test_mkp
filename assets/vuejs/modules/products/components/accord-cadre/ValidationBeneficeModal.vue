<template>
  <div class="modal-overlay">
    <div
      class="z-9999 fixed top-[45%] mx-6 flex max-w-[500px] flex-col items-center justify-center rounded-xl bg-secondary p-4 text-center text-white !opacity-100 md:mx-0"
    >
      <div class="relative flex w-full justify-end">
        <button class="right-2 text-lg text-white" @click="emit('cancel')">
          X
        </button>
      </div>
      <h3 class="text-base md:text-lg lg:text-2xl">Merci!</h3>
      <div class="pt-3 pb-6 text-sm md:text-base lg:text-lg">
        Nous avons bien pris connaissance de votre demande pour bénéficier des
        conditions du partenaires
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
const emit = defineEmits(['cancel'])
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
