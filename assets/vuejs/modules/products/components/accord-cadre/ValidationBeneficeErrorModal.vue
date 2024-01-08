<template>
  <div class="modal-overlay">
    <div
      class="z-9999 fixed top-[50%] left-[40%] ml-[-150px] flex w-[600px] flex-col items-center justify-center rounded-xl bg-secondary px-2 py-3 text-center text-white !opacity-100"
    >
      <div class="relative mb-3 flex w-full justify-end">
        <button class="right-2 text-lg text-white" @click="emit('cancel')">
          X
        </button>
      </div>
      <h3 class="text-base md:text-lg lg:text-2xl">
        Une erreur s’est produite
      </h3>
      <div class="pt-3 pb-6 text-sm md:text-base lg:text-lg">
        Votre demande pour bénéficier des conditions n’a pas pu aboutir.
        Veuillez réessayer plus tard ou nous contacter
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
