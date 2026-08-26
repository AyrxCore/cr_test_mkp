<template>
  <div class="mt-4 w-full justify-center sm:mt-10 xl:mt-14">
    <div
      class="mx-4 flex flex-col justify-center text-center text-white sm:text-gray-500"
    >
      <p class="px-12 text-sm">
        {{ customerService }} est à votre écoute <br />
        du lundi au vendredi de 8h30 à 18h au
        <a
          :href="`tel:${channel?.phoneNumber}`"
          class="text-secondary underline lg:text-right"
          >{{ channelPhoneNumber }}
        </a>
        ou par mail
      </p>
      <ButtonComponent
        class="button-primary-outline mx-auto mt-5 w-full items-center px-6! text-primary! sm:flex sm:w-auto sm:px-8 lg:mt-10"
        type="button"
        @click="EmailContactGtmEvent()"
      >
        <MailIcon
          class="h-[15px] w-[15px] fill-primary stroke-white text-secondary hover:fill-secondary!"
        />
        <span>{{ contactAdherentsService }}</span>
      </ButtonComponent>
    </div>
    <ContactModal
      v-if="showContactForm"
      :is-loading="isLoading"
      class="modal"
      @cancel="showContactForm = false"
    />
  </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'

import { useChannelStore } from '@/vuejs/stores/channel'

import ContactModal from '@/vuejs/modules/contact/component/ContactModal.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import MailIcon from '@/vuejs/modules/shared/icon/MailIconComponent.vue'

const { channel, formattedPhoneNumber: channelPhoneNumber } =
  storeToRefs(useChannelStore())

const showContactForm = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const EmailContactGtmEvent = () => {
  showContactForm.value = true
}

const customerService = computed((): string => {
  return (
    channel?.value?.options
      ?.PRE_HOME_TEXT_FIRST_CONNECTION_CHANGE_OF_PASSWORD ??
    'Notre service adhérents'
  )
})

const contactAdherentsService = computed((): string => {
  return (
    channel?.value?.options
      ?.PRE_HOME_BUTTON_FIRST_CONNECTION_CHANGE_OF_PASSWORD ??
    'Joindre le service adhérents par email'
  )
})
</script>
