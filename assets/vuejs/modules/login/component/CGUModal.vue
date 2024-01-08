<template>
  <div class="modal-overlay">
    <div
      class="z-9999 fixed top-[5%] left-[30%] ml-[-150px] flex h-[90%] w-full flex-col items-center justify-start overflow-auto rounded-xl bg-white px-10 py-3 text-white !opacity-100 lg:w-[50%]"
    >
      <CmsPageComponent :page-id="CGU_PAGE_ID" />
      <div class="mt-5 flex flex-col items-center space-x-3 md:flex-row">
        <label
          :class="{
            'text-red-400': showAlertModal,
          }"
          class="cursor-pointer text-xs text-primary md:text-base"
        >
          <input v-model="acceptCGU" type="checkbox" class="mr-2" />
          J'accepte les CGU de la marketplace QANTIS
        </label>

        <ButtonComponent class="button-primary mt-3 md:mt-0" @click="validCGU">
          Continuer
        </ButtonComponent>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { CGU_PAGE_ID } from '@/vuejs/services/const'
import CmsPageComponent from '../../shared/CmsPageComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'

const showAlertModal = ref<boolean>(false)
const acceptCGU = ref(false)
const emit = defineEmits(['valid-cgu'])

const validCGU = () => {
  if (acceptCGU.value) {
    emit('valid-cgu')
  } else {
    showAlertModal.value = true
    return false
  }
}
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
