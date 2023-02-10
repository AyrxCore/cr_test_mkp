<template>
    <div
      class="modal-overlay"
    >
      <div
        class="fixed bg-secondary p-2 rounded-xl text-white flex flex-col justify-center items-center
      text-center top-[50%] left-[45%] w-[500px] ml-[-150px] !opacity-100 z-9999">
        <div class="flex justify-end relative">
          <button class="button-secondary text-lg right-2 text-white" @click="emit('cancel')">
            X
          </button>
        </div>
        <h3>Merci!</h3>
        <div class="py-6">
          Nous avons bien pris connaissance de votre demande pour bénéficier des conditions du partenaires
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
