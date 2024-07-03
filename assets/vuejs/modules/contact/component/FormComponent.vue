<template>
  <!-- Bloc formulaire -->
  <div v-if="contact">
    <MessageSquareIconComponent
      class="mx-auto mb-2 w-full stroke-secondary"
      :width="40"
      :height="40"
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
            @change="
              sendGaEvent('click_contact_demande_type', {
                demande_value: motifs[contact.motif],
              })
            "
          >
            <option disabled value="" class="text-gray-500">
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
                type="text"
                placeholder="Votre nom *"
                class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 placeholder-gray-400"
                required
              />
            </div>
            <div class="mb-3 pt-0">
              <input
                v-model="contact.firstName"
                type="text"
                placeholder="Votre prénom *"
                class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 placeholder-gray-400"
                required
              />
            </div>
            <div class="mb-3 pt-0">
              <input
                v-model="contact.email"
                type="email"
                placeholder="Votre email *"
                title="Ex: votre_email@test.fr"
                class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 placeholder-gray-400"
                required
              />
            </div>
            <div class="flex flex-col pt-0">
              <input
                v-model="contact.phone"
                type="tel"
                pattern="^((\+)33|0)[1-9](\d{2}){4}$"
                title="Ex: 0478123456"
                placeholder="Votre téléphone (Optionnel)"
                class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 placeholder-gray-400"
              />
            </div>
          </div>
          <div class="mt-3 h-[255px] w-full lg:mt-0 lg:w-1/2">
            <div class="mb-3 flex flex-col pt-0">
              <input
                v-model="contact.accordCadreName"
                type="text"
                placeholder="Nom partenaire / accord-cadre (Optionnel)"
                class="border-1 relative h-[55px] w-full rounded-lg border-gray-200 bg-white px-3 placeholder-gray-400"
              />
            </div>
            <div class="pt-0">
              <textarea
                v-model="contact.description"
                placeholder="Votre message *"
                class="border-1 relative h-full w-full resize-none rounded-lg border-gray-200 bg-white px-3 py-3 placeholder-gray-400"
                rows="7"
                required
              />
            </div>
          </div>
        </div>
        <div class="mt-2 flex justify-end">
          <ButtonComponent
            type="submit"
            class="button-primary mt-2 md:w-auto"
            :is-loading="isLoading"
            @click="sendGaEvent('click_contact_send')"
          >
            <ArrowRightIconComponent
              class="mr-2 w-4"
              :stroke="betterTextColor('primary')"
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
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import MessageSquareIconComponent from '@/vuejs/modules/shared/icon/MessageSquareIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import { useContactStore } from '@/vuejs/stores/contact'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import { betterTextColor } from '@/vuejs/services/utils'

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

  isLoading.value = false
}
</script>

<style scoped></style>
