<template>
  <!-- Bloc formulaire -->
  <div v-if="contact">
    <MessageSquareIconComponent
      :height="40"
      :width="40"
      class="mx-auto mb-2 w-full stroke-secondary"
    />
    <h4 class="mb-3 text-center text-2xl font-bold text-primary">
      Directement en nous laissant un message
    </h4>
    <div v-if="alertStore.show" class="text-left">
      <AlertSharedComponent />
    </div>
    <div class="app-advanced">
      <form class="mx-auto w-full" @submit.prevent="sendEmail">
        <div class="mb-3 pt-0">
          <select
            v-model="contact.motif"
            class="border-1 relative h-[55px] w-full rounded-md border-gray-200"
            required
          >
            <option class="text-gray-500" disabled value="">
              Votre demande concerne *
            </option>
            <option
              v-for="(motif, index) in motifs"
              :key="index"
              :value="index"
            >
              {{ motif }}
            </option>
          </select>
        </div>
        <div class="flex flex-col lg:flex-row lg:space-x-2">
          <div class="w-full lg:w-1/2">
            <div class="mb-3 pt-0">
              <input
                v-model="contact.lastName"
                class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 placeholder-gray-400"
                placeholder="Votre nom *"
                required
                type="text"
              />
            </div>
            <div class="mb-3 pt-0">
              <input
                v-model="contact.firstName"
                class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 placeholder-gray-400"
                placeholder="Votre prénom *"
                required
                type="text"
              />
            </div>
            <div class="mb-3 pt-0">
              <input
                v-model="contact.email"
                class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 placeholder-gray-400"
                placeholder="Votre email *"
                required
                title="Ex: votre_email@test.fr"
                type="email"
              />
            </div>
            <div class="flex flex-col pt-0">
              <input
                v-model="contact.phone"
                class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 placeholder-gray-400"
                pattern="^((\+)33|0)[1-9](\d{2}){4}$"
                placeholder="Votre téléphone (Optionnel)"
                title="Ex: 0478123456"
                type="tel"
              />
            </div>
          </div>
          <div class="mt-3 h-[255px] w-full lg:mt-0 lg:w-1/2">
            <div class="mb-3 flex flex-col pt-0">
              <input
                v-model="contact.accordCadreName"
                class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 placeholder-gray-400"
                placeholder="Nom partenaire / accord-cadre (Optionnel)"
                type="text"
              />
            </div>
            <div class="pt-0">
              <textarea
                v-model="contact.description"
                class="border-1 relative h-full w-full resize-none rounded-lg border-gray-200 bg-white px-3 py-3 placeholder-gray-400"
                placeholder="Votre message *"
                required
                rows="7"
              />
            </div>
          </div>
        </div>
        <div class="mt-2 flex justify-end">
          <ButtonComponent
            :is-loading="isLoading"
            class="button-primary mt-2 md:w-auto"
            type="submit"
          >
            <ArrowRightIconComponent
              :stroke="betterTextColor('primary')"
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
import { computed, onMounted, ref } from 'vue'

import { useContactStore } from '@/vuejs/stores/contact'
import { useAlertStore } from '@/vuejs/stores/alert'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { betterTextColor } from '@/vuejs/services/utils'
import { AlertType } from '@/vuejs/types/Alert'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import MessageSquareIconComponent from '@/vuejs/modules/shared/icon/MessageSquareIconComponent.vue'

onMounted(async () => {
  await contactStore.init()
})

const alertStore = useAlertStore()
const contactStore = useContactStore()
const isLoading = ref<boolean>(false)

const contact = computed(() => {
  return contactStore.contact
})

const motifs = computed(() => {
  return contactStore.motifs
})

const sendEmail = async () => {
  isLoading.value = true
  const alertStore = useAlertStore()
  const response = await contactStore.sendEmail(contact.value)
  if (response.error === false) {
    await contactStore.init()
  }
  alertStore.setShow(
    response.message,
    response.error === true ? AlertType.danger : AlertType.success,
  )
  sendGtmEvent('contact_form_submission', {
    form_type: 'contact',
    subject: motifs[contact.value.motif],
  })

  isLoading.value = false
}
</script>
