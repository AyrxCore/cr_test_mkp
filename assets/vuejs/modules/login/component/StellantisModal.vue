<template>
  <div class="modal-overlay">
    <div
      v-click-outside="() => closeModalStellantis"
      class="mx-auto my-auto max-h-[90vh] w-[90%] overflow-scroll rounded-xl bg-white px-4 py-6 text-black sm:w-[80%] md:w-[70%] lg:w-[50%] lg:px-8"
    >
      <div class="flex justify-end">
        <ButtonComponent
          class="h-5! px-3! text-xl font-bold text-primary!"
          type="button"
          @click="closeModalStellantis"
        >
          <CloseIconComponent />
        </ButtonComponent>
      </div>
      <h2 class="mb-6 mt-2 text-center text-xl font-bold sm:text-2xl">
        Conditions particulières Stellantis
      </h2>

      <div class="space-y-4 text-sm">
        <p>
          Pour bénéficier de nos protocoles Stellantis sur les marques Jeep,
          Alfa Romeo, Opel, Peugeot, DS, Fiat, Citroën, Lancia et Abarth et
          demander à être rattaché au contrat QANTIS, vous vous engagez :
        </p>

        <ol class="list-inside list-decimal space-y-2 pl-4">
          <li>
            À immatriculer les véhicules en France et les utiliser à titre
            principal en France.
          </li>
          <li>
            À ce que chacun des véhicules soit utilisé par les salariés
            principalement pour leurs besoins professionnels.
          </li>
          <li>
            À ce que chaque véhicule soit conservé pour une période minimum de
            douze mois et ne soit pas revendu avant l'expiration de ce délai de
            conservation.
          </li>
        </ol>

        <p class="font-medium">
          Les entreprises ayant comme activité principale les métiers ci-après
          ne peuvent pas prétendre à bénéficier des accords auprès de Stellantis
          : carrossiers, taxis, VTC, auto-écoles, courtiers automobiles,
          distributeurs automobile, locations automobiles, établissements
          publics.
        </p>
      </div>

      <div class="mt-5 flex items-center justify-center">
        <label
          :class="{
            'text-red-400': showAlert,
          }"
          class="cursor-pointer text-xs text-primary sm:text-sm md:text-base"
        >
          <input v-model="acceptStellantis" class="mr-2" type="checkbox" />
          J'accepte les conditions et je demande mon rattachement
        </label>
      </div>

      <div class="mt-6 flex justify-center">
        <ButtonComponent
          :is-loading="isLoading"
          class="button-primary w-auto"
          @click="validateStellantis"
        >
          Valider
        </ButtonComponent>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { onBeforeMount, onBeforeUnmount, ref } from 'vue'

import router from '@/vuejs/router'
import { sendGtmEvent } from '@/vuejs/services/gtm'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CloseIconComponent from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'

defineProps({
  isLoading: {
    type: Boolean,
    default: false,
  },
})

const showAlert = ref<boolean>(false)
const acceptStellantis = ref<boolean>(false)
const emit = defineEmits(['accept-stellantis', 'cancel-stellantis'])

const validateStellantis = () => {
  if (acceptStellantis.value) {
    emit('accept-stellantis')
    sendGtmEvent('modal_stellantis_cta_click', {
      origin_url: router.currentRoute.value.fullPath,
    })
  } else {
    showAlert.value = true
    return false
  }
}

const closeModalStellantis = () => {
  emit('cancel-stellantis')
  sendGtmEvent('modal_stellantis_close')
}

onBeforeMount((): void => {
  document.body.style.overflow = 'hidden'
})
onBeforeUnmount((): void => {
  document.body.style.overflow = 'initial'
})
</script>
