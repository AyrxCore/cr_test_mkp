<template>
  <div class="modal-overlay">
    <div
      class="z-9999 fixed top-[25%] left-[45%] ml-[-150px] flex w-[500px] flex-col items-center justify-center rounded-xl bg-secondary px-3 py-3 text-center text-white !opacity-100"
    >
      <div class="relative mb-3 flex w-full justify-end">
        <button class="right-2 text-lg text-white" @click="emit('cancel')">
          X
        </button>
      </div>
      <h3 class="text-base md:text-lg lg:text-2xl">Attention</h3>
      <div class="pt-3 pb-6 text-sm md:text-base lg:text-lg">
        Vous vous apprêtez à modifier votre adresse email de connexion à la
        marketplace.<br />
        Un email va vous être adressé sur l’email que vous venez de renseigner
        afin de valider ce changement<br />
        Etes-vous sûr de vouloir continuer ?
      </div>
      <div
        class="border-white-200 flex items-center space-x-2 rounded-b border-t p-6 dark:border-white"
      >
        <ButtonComponent
          data-modal-hide="defaultModal"
          class="button-primary-outline !hover:border mr-2 !border"
          type="button"
          @click="emit('cancel')"
        >
          Annuler
        </ButtonComponent>
        <ButtonComponent
          data-modal-hide="defaultModal"
          class="button-primary !hover:border mr-2 !border"
          type="button"
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
