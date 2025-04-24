<template>
  <form class="text-xl" @submit.prevent="onFormSubmit">
    <div class="mb-6 grid grid-cols-2 items-center md:grid-cols-3">
      <div class="text-xl font-bold text-primary">Partenaire&nbsp;:</div>
      <input
        :value="product.seller.name"
        class="block w-full rounded-lg border-gray-300 bg-gray-200 p-2.5 text-gray-400"
        disabled
        readonly
        type="text"
      />
    </div>
    <div class="mb-8 grid grid-cols-2 items-center md:grid-cols-3">
      <div class="text-xl font-bold text-primary">
        Produit/service concerné&nbsp;:
      </div>
      <input
        :value="product.name"
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
            v-model="user.account.phone"
            class="block w-full rounded-lg border-gray-300 p-2.5"
            type="tel"
          />
          <div
            v-if="hasPhoneError"
            class="absolute mt-1 flex items-center text-sm text-red-500"
          >
            <AlertCircleOutlineIconComponent class="mr-1" />
            Merci de renseigner un numéro de téléphone valide
          </div>
        </div>
      </div>
      <div
        v-if="product?.notSellableFormWithMessage"
        class="md:grid-rows flex flex-col md:grid md:grid-flow-col md:gap-12"
      >
        <div class="mb-6">
          <label class="flex w-full justify-start text-lg text-gray-700">
            Votre message
          </label>
          <textarea
            v-model="optionnalMessage"
            class="block h-[100px] w-full rounded-lg border-gray-300 p-2.5"
            placeholder="Veuillez préciser votre besoin au partenaire (optionnel)"
          />
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
import { computed, PropType, ref } from 'vue'
import { storeToRefs } from 'pinia'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CheckCircleIconComponent from '@/vuejs/modules/shared/icon/CheckCircleIconComponent.vue'
import AlertCircleOutlineIconComponent from '@/vuejs/modules/shared/icon/AlertCircleOutlineIconComponent.vue'

import { Product } from '@/vuejs/types/Product'
import { useUserStore } from '@/vuejs/stores/user'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'
import { notifyError } from '@/vuejs/services/utils'

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const { user } = storeToRefs(useUserStore())

const loadingSubmit = ref<boolean>(false)
const optionnalMessage = ref<string>(null)
const requestContactSent = ref<boolean>(false)

const hasPhoneError = computed(() => {
  const phoneRegex = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/
  return !phoneRegex.test(user.value.account.phone)
})

const onFormSubmit = async () => {
  try {
    if (hasPhoneError.value) {
      return
    }
    loadingSubmit.value = true

    const data = {
      accordId: props.product.properties['accord-id'],
      accordName: props.product.properties['accord-name'],
      email: user.value.email,
      phone: user.value.account.phone,
      product: props.product.name,
      partner: props.product.seller.name,
      message: optionnalMessage.value,
    }

    await ProductHttpClient.get().sendContactRequestFromNotSellableProduct(data)
    requestContactSent.value = true
  } catch (error) {
    notifyError(
      'Une erreur est survenue lors de votre demande de contact, veuillez contacter le service technique',
    )
  } finally {
    loadingSubmit.value = false
  }
}
</script>
