<template>
  <form class="text-xl" @submit.prevent="onFormSubmit">
    <div class="mb-6 grid grid-cols-2 items-center md:grid-cols-3">
      <div class="text-xl font-bold text-primary">Partenaire&nbsp;:</div>
      <input
        :value="partnerName"
        class="block w-full rounded-lg border-gray-300 bg-gray-200 p-2.5 text-gray-400"
        disabled
        readonly
        type="text"
      />
    </div>
    <div class="mb-8 grid grid-cols-2 items-center md:grid-cols-3">
      <div class="text-xl font-bold text-primary">
        {{ productLabel }}&nbsp;:
      </div>
      <input
        :value="productName"
        class="block w-full rounded-lg border-gray-300 bg-gray-200 p-2.5 text-gray-400"
        disabled
        readonly
        type="text"
      />
    </div>
    <div>
      <div class="mb-4 text-xl font-bold text-primary">
        Vos coordonnées&nbsp;:
      </div>
      <div class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-6">
        <div class="mb-6">
          <label class="flex w-full justify-start text-lg text-gray-700">
            Nom
          </label>
          <input
            :value="user.lastName"
            class="block w-full rounded-lg border-gray-300 bg-gray-200 p-2.5 text-gray-400"
            disabled
            readonly
            type="text"
          />
        </div>
        <div class="mb-6">
          <label class="flex w-full justify-start text-lg text-gray-700">
            Prénom
          </label>
          <input
            :value="user.firstName"
            class="block w-full rounded-lg border-gray-300 bg-gray-200 p-2.5 text-gray-400"
            disabled
            readonly
            type="text"
          />
        </div>
      </div>
      <div class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-6">
        <div class="mb-6">
          <label class="flex w-full justify-start text-lg text-gray-700">
            Adresse email
          </label>
          <input
            :value="user.email"
            class="block w-full rounded-lg border-gray-300 bg-gray-200 p-2.5 text-gray-400"
            disabled
            readonly
            type="text"
          />
        </div>
        <div class="relative mb-6">
          <label class="flex w-full justify-start text-lg text-gray-700">
            Numéro de téléphone <span class="ml-1 text-red-600">*</span>
          </label>
          <input
            v-model="phoneNumber"
            class="block w-full rounded-lg border-gray-300 p-2.5"
            type="tel"
            @blur="phoneTouched = true"
          />
          <div
            v-if="phoneTouched && hasPhoneError"
            class="absolute mt-1 flex items-center text-sm text-red-500"
          >
            <AlertCircleOutlineIconComponent class="mr-1" />
            Merci de renseigner un numéro de téléphone valide
          </div>
        </div>
      </div>
      <div
        v-if="showMessage"
        class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-12"
      >
        <div class="mb-6">
          <label class="flex w-full justify-start text-lg text-gray-700">
            Votre message
            <span v-if="messageRequired" class="ml-1 text-red-600">*</span>
          </label>
          <textarea
            v-model="optionalMessage"
            class="block h-[100px] w-full rounded-lg border-gray-300 p-2.5"
            :placeholder="messagePlaceholder"
            @blur="messageTouched = true"
          />
          <div
            v-if="messageRequired && messageTouched && hasMessageError"
            class="mt-1 flex items-center text-sm text-red-500"
          >
            <AlertCircleOutlineIconComponent class="mr-1" />
            Merci de préciser votre besoin
          </div>
        </div>
      </div>
      <div class="flex justify-center">
        <ButtonComponent
          v-if="!requestContactSent"
          :is-loading="loadingSubmit"
          class="button-primary"
        >
          Envoyer ma demande
        </ButtonComponent>
        <div v-else class="flex items-center">
          <CheckCircleIconComponent class="mr-2" color="green" />
          Demande envoyée
        </div>
      </div>
    </div>
  </form>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'

import { useUserStore } from '@/vuejs/stores/user'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'
import { notifyError } from '@/vuejs/services/utils'
import { sendGtmEvent } from '@/vuejs/services/gtm'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CheckCircleIconComponent from '@/vuejs/modules/shared/icon/CheckCircleIconComponent.vue'
import AlertCircleOutlineIconComponent from '@/vuejs/modules/shared/icon/AlertCircleOutlineIconComponent.vue'

export interface ContactFormData {
  accordId: string
  productName: string
  partnerName: string
}

export interface GtmEventConfig {
  eventName: string
  eventData: Record<string, unknown>
}

const props = withDefaults(
  defineProps<{
    formData: ContactFormData
    gtmEvent: GtmEventConfig
    productLabel?: string
    showMessage?: boolean
    messageRequired?: boolean
    messagePlaceholder?: string
    closeOnSuccess?: boolean
    closeDelay?: number
  }>(),
  {
    productLabel: 'Produit/service concerné',
    showMessage: false,
    messageRequired: false,
    messagePlaceholder:
      'Veuillez préciser votre besoin au partenaire (optionnel)',
    closeOnSuccess: false,
    closeDelay: 2000,
  },
)

const emit = defineEmits<{
  success: []
}>()

const { user } = storeToRefs(useUserStore())

const loadingSubmit = ref<boolean>(false)
const optionalMessage = ref<string>('')
const requestContactSent = ref<boolean>(false)
const phoneNumber = ref<string>(user.value.account?.phone || '')
const phoneTouched = ref<boolean>(false)
const messageTouched = ref<boolean>(false)

const partnerName = computed(() => props.formData.partnerName)
const productName = computed(() => props.formData.productName)

const hasPhoneError = computed(() => {
  const phoneRegex = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/
  return !phoneRegex.test(phoneNumber.value)
})

const hasMessageError = computed(() => {
  return !optionalMessage.value || optionalMessage.value.trim().length === 0
})

const onFormSubmit = async () => {
  try {
    phoneTouched.value = true
    if (props.messageRequired) {
      messageTouched.value = true
    }

    if (
      hasPhoneError.value ||
      (props.messageRequired && hasMessageError.value)
    ) {
      return
    }
    loadingSubmit.value = true

    const data = {
      accordId: props.formData.accordId,
      email: user.value.email,
      phone: phoneNumber.value,
      product: props.formData.productName,
      partner: props.formData.partnerName,
      message: optionalMessage.value,
    }

    await ProductHttpClient.get().sendContactRequestFromNotSellableProduct(data)
    sendGtmEvent(props.gtmEvent.eventName, props.gtmEvent.eventData)
    requestContactSent.value = true

    if (props.closeOnSuccess) {
      setTimeout(() => {
        emit('success')
      }, props.closeDelay)
    }
  } catch (_error) {
    notifyError(
      'Une erreur est survenue lors de votre demande de contact, veuillez contacter le service technique',
    )
  } finally {
    loadingSubmit.value = false
  }
}
</script>
