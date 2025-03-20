<template>
  <div class="modal-overlay">
    <div
      class="mx-auto my-auto max-h-[90vh] w-[90%] rounded-xl bg-white px-4 py-6 text-black sm:w-[80%] md:w-[70%] lg:w-[50%] lg:px-8"
    >
      <h2 class="mb-6 mt-2 text-center text-xl font-bold sm:text-2xl">
        Conditions particulières Stellantis
      </h2>

      <div class="space-y-4 text-sm">
        <p>
          Pour bénéficier de nos protocoles Stellantis sur les marques Jeep,
          Alfa Romeo, Opel, Peugeot, DS, Fiat et Citroën et demander à être
          rattaché au contrat QANTIS, vous vous engagez :
        </p>

        <ol class="list-inside list-decimal space-y-2 pl-4">
          <li>
            A immatriculer les véhicules en France et les utiliser à titre
            principal en France.
          </li>
          <li>
            A ce que chacun des véhicules soit utilisé par les salariés
            principalement pour leurs besoins professionnels.
          </li>
          <li>
            A ce que chaque véhicule soit conservé pour une période minimum de
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
          <input v-model="acceptStellantis" type="checkbox" class="mr-2" />
          J'accepte les conditions et je demande mon rattachement
        </label>
      </div>

      <div class="mt-6 flex justify-center">
        <ButtonComponent
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
import { ref } from 'vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'

const showAlert = ref<boolean>(false)
const acceptStellantis = ref(false)
const emit = defineEmits(['accept-stellantis'])

const validateStellantis = () => {
  if (acceptStellantis.value) {
    emit('accept-stellantis')
  } else {
    showAlert.value = true
    return false
  }
}
</script>

<style scoped></style>
