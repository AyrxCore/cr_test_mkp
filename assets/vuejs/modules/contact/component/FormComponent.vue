<template>
  <!-- Bloc formulaire -->
  <div v-if="contact">
    <MessageSquareIconComponent
      :stroke-color="'#9553FF'"
      class="mx-auto mb-2 w-full"
    />
    <h4 class="text-primary mb-3 font-bold text-center">
      Directement en nous laissant un message
    </h4>
    <div v-if="alertStore.show" class="text-left">
      <AlertSharedComponent />
    </div>
    <div class="app-advanced">
      <form class="mx-auto w-full" @submit.prevent="sendEmail">
        <div class="mb-3 pt-0">
          <input type="hidden"  name="_token" :value="contact._token" />
          <select
            v-model="contact.motif"
            class="relative h-[55px] w-full rounded-md border-1 border-gray-200 text-gray-600 placeholder-gray-400"
            required
          >
            <option disabled value="" class="text-gray-500">Votre demande concerne *</option>
            <option
              v-for="(motif, index) in motifs"
              :key="index"
              :value="index"
            >{{ motif }}</option>
          </select>
        </div>
        <div class="flex flex-col lg:flex-row lg:space-x-2">
          <div class="h-[255px] w-full lg:w-1/2">
            <div class="mb-3 pt-0">
              <input
                v-model="contact.lastName"
                type="text"
                placeholder="Votre nom *"
                class="relative h-[55px] w-full rounded-lg bg-white px-3 text-gray-600 placeholder-gray-400 border-1 border-gray-200"
                required
              />
            </div>
            <div class="mb-3 pt-0">
              <input
                v-model="contact.firstName"
                type="text"
                placeholder="Votre prénom *"
                class="relative h-[55px] w-full rounded-lg bg-white px-3 text-gray-600 placeholder-gray-400 border-1 border-gray-200"
                required
              />
            </div>
            <div class="mb-3 pt-0">
              <input
                v-model="contact.email"
                type="email"
                placeholder="Votre email *"
                class="relative h-[55px] w-full rounded-lg bg-white px-3 text-gray-600 placeholder-gray-400 border-1 border-gray-200"
                required
              />
            </div>
            <div class="mb-3 pt-0 flex flex-col ">
              <input
                v-model="contact.phone"
                type="tel"
                placeholder="Votre téléphone (Optionnel)"
                class="relative h-[55px] w-full rounded-lg bg-white px-3 text-gray-600 placeholder-gray-400 border-1 border-gray-200"
                @blur="checkPhoneNumber"
              />
              <span
                v-if="isNotValidPhoneNumber"
                class="text-red-700 bg-red-100 bg-red-200 w-full p-1 flex"
              >
                Le numéro de téléphone n'est pas correct
              </span>
            </div>
          </div>
          <div class="h-[255px] w-full lg:w-1/2 mt-3 lg:mt-0">
            <div class="mb-3 pt-0 flex flex-col ">
              <input
                v-model="contact.accordCadreName"
                type="text"
                placeholder="Nom partenaire / accord-cadre (Optionnel)"
                class="relative h-[55px] w-full rounded-lg bg-white px-3 text-gray-600 placeholder-gray-400 border-1 border-gray-200"
              />
            </div>
            <div class="pt-0">
              <textarea
                v-model="contact.description"
                placeholder="Votre message *"
                class="relative h-full w-full resize-none rounded-lg bg-white px-3 py-3
                 text-gray-600 placeholder-gray-400 border-1 border-gray-200"
                rows="7"
                required
              />
            </div>
          </div>
        </div>
        <div class="flex justify-end mt-2">
          <ButtonComponent
            type="submit"
            class="button button-gradient mt-2 justify-center text-center w-full md:w-auto"
            :disabled="isLoading"
          >
            <div v-show="isLoading">
              <LoaderSharedComponent />
            </div>
            <ArrowRightIconComponent
              :stroke-color="'#FFFFFF'"
              class="mr-2 w-4"
            />
            Envoyer
          </ButtonComponent>
        </div>
      </form>
    </div>
  </div>
  <!-- Fin Bloc formulaire -->
</template>
<script lang="ts" setup>
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import MessageSquareIconComponent from '@/vuejs/modules/shared/icon/MessageSquareIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { computed, onMounted, ref } from 'vue'
import { useContactStore } from '@/vuejs/stores/contact'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

onMounted(async () => {
  await contactStore.init()
})

const alertStore = useAlertStore()
const contactStore = useContactStore()
const isLoading = ref<boolean>(false)

const isNotValidPhoneNumber = ref<boolean>(false)

const contact = computed(() => {
  return contactStore.contact
})

const motifs = computed(() => {
  return contactStore.motifs
})

const checkPhoneNumber = (() => {
  if (contact.value.phone) {
    // Expression régulière pour un numéro de téléphone français
    const telephoneRegex = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/
    isNotValidPhoneNumber.value = !telephoneRegex.test(contact.value.phone)
  } else {
    isNotValidPhoneNumber.value = false
  }
})

const sendEmail = (async () => {
  if (isNotValidPhoneNumber.value) {
    return false
  }
  isLoading.value = true
  setTimeout(async () => {
    const alertStore = useAlertStore()
    const response = await contactStore.sendEmail(contact.value)
    if (response.error === false) {
      await contactStore.init()
    }
    alertStore.setShow(
      response.message,
      response.error === true ? AlertType.danger : AlertType.success,
    )

    isLoading.value = false
  },500 )
})
</script>

<style scoped></style>
