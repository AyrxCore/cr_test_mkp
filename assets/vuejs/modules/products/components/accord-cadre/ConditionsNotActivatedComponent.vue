<template>
  <div>
    <ButtonComponent
      class="button-gradient mb-7"
      :is-loading="isLoading"
      @click="sendSubmission"
    >
      <ArrowRightIconComponent />
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

  <div class="condition-beneficiaire">
    <p v-html="text" />
  </div>
</template>
<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ModalValidationBeneficePartnerModal from '@/vuejs/modules/products/components/accord-cadre/ValidationBeneficeModal.vue'
import ModalValidationBeneficeErrorModal from '@/vuejs/modules/products/components/accord-cadre/ValidationBeneficeErrorModal.vue'

import { PropType, ref } from 'vue'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'
import { status } from '@/vuejs/modules/products'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'

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
