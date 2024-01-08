<template>
  <div class="mt-4 w-full justify-center sm:mt-10 xl:mt-14">
    <div
      class="mx-4 flex flex-col justify-center text-center text-white sm:text-gray-500"
    >
      <p class="px-12 text-sm">
        Notre service adhérents est à votre écoute <br />
        du lundi au vendredi de 8h30 à 18h au
        <a
          :href="`tel:${channel?.phoneNumber}`"
          class="text-secondary underline lg:text-right"
          @click="sendGaEvent('click_prehome_tel')"
          >{{ channelPhoneNumber }}</a
        >
        ou par mail
      </p>
      <ButtonComponent
        type="button"
        class="button-primary-outline mx-auto mt-5 w-full items-center !px-6 !text-primary sm:flex sm:w-auto sm:px-8 lg:mt-10"
        @click="EmailContactGtmEvent()"
      >
        <MailIcon
          class="h-[15px] w-[15px] fill-primary stroke-white text-secondary hover:!fill-secondary"
        />
        <span>Joindre le service adhérents par email</span>
      </ButtonComponent>
    </div>
    <ContactModal
      v-if="showContactForm"
      class="modal"
      :is-loading="isLoading"
      @cancel="showContactForm = false"
    />
  </div>
</template>
<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { useChannelStore } from '@/vuejs/stores/channel'
import { ref } from 'vue'
import MailIcon from '@/vuejs/modules/shared/icon/MailIconComponent.vue'
import ContactModal from '@/vuejs/modules/contact/component/ContactModal.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const { channel, formattedPhoneNumber: channelPhoneNumber } = storeToRefs(
  useChannelStore(),
)

const showContactForm = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const EmailContactGtmEvent = () => {
  showContactForm.value = true
  sendGaEvent('click_prehome_email')
}
</script>
