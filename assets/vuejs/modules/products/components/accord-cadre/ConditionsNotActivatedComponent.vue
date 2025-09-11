<template>
  <div class="text-center text-lg leading-5 text-white">
    <div class="condition-beneficiaire mb-7">
      <p v-html="text" />
    </div>
    <ButtonComponent
      :class="{
        disabled: isNeoAutoLogin,
      }"
      :is-loading="isLoading"
      class="button-primary mb-7 whitespace-normal border-2 border-solid !border-white"
      @click="sendSubmission"
    >
      <ArrowRightIconComponent class="h-4 w-4" />
      {{ label ?? 'Bénéficiez des conditions' }}
    </ButtonComponent>
    <ModalValidationBeneficePartnerModal
      v-if="showSuccesModal"
      class="modal"
      @cancel="closeModal"
    />
    <ModalValidationBeneficeErrorModal
      v-if="showErrorModal"
      class="modal"
      @cancel="closeModal"
    />
  </div>
</template>

<script lang="ts" setup>
import { PropType, ref } from 'vue'
import { storeToRefs } from 'pinia'

import router from '@/vuejs/router.ts'
import { useUserStore } from '@/vuejs/stores/user'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'
import { status } from '@/vuejs/modules/products'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ModalValidationBeneficePartnerModal from '@/vuejs/modules/products/components/accord-cadre/ValidationBeneficeModal.vue'
import ModalValidationBeneficeErrorModal from '@/vuejs/modules/products/components/accord-cadre/ValidationBeneficeErrorModal.vue'
import { sendGtmEvent } from '@/vuejs/services/gtm'

const { isNeoAutoLogin } = storeToRefs(useUserStore())

const showSuccesModal = ref<boolean>(false)
const showErrorModal = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const props = defineProps({
  currentStatus: {
    type: Object as PropType<AccountAccordCadre>,
    required: true,
  },
  text: {
    type: String,
    default: null,
  },
  url: {
    type: String,
    default: null,
  },
  accordName: {
    type: String,
    default: null,
  },
  label: {
    type: String,
    default: null,
  },
})

const sendSubmission = async () => {
  isLoading.value = true
  try {
    const response =
      await ProductHttpClient.get().updateAccountAccordsCadresByParams({
        accordId: props.currentStatus.accordId,
        accordName: props.accordName,
      })

    if (status.value.pending === response) {
      showSuccesModal.value = true
      sendGtmEvent('fat_cta_rattachement_click', {
        link_text: props.label ?? 'Bénéficiez des conditions',
        origin_url: router.currentRoute.value.fullPath,
      })
    } else {
      showErrorModal.value = true
    }
  } catch (error) {
    isLoading.value = false
  }
}

const closeModal = () => {
  if (showSuccesModal.value) {
    props.currentStatus.status = status.value.pending
  }
  isLoading.value = false
  showSuccesModal.value = false
  showErrorModal.value = false
}
</script>
