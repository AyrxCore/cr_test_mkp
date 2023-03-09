<template>
  <div>
    <div>
      <ButtonComponent
          class="button-gradient mb-7"
          :is-loading="isLoading"
          @click="sendSubmission"
      >
        <ArrowRightIconComponent/>
        Bénéficiez des conditions
      </ButtonComponent>
      <ModalValidationBeneficePartnerModal
          v-if="showModal"
          class="modal"
          @cancel="closeModal"
      />
    </div>

    <div class="condition-beneficiaire">
      <p v-html="text"/>
    </div>
  </div>
</template>
<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ModalValidationBeneficePartnerModal
  from '@/vuejs/modules/products/components/accord-cadre/ValidationBeneficeModal.vue'
import { PropType, ref } from 'vue'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'
import { status } from '@/vuejs/modules/products'
import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'

const showModal = ref<boolean>(false)
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
})

const sendSubmission = (async () => {
  isLoading.value = true
  try {
    await ProductHttpClient.get().updateAccountAccordsCadresByParams(
      {
        accordId: props.currentStatus.accordId,
      },
  )
    showModal.value = true
  } catch (error) {
    isLoading.value = false
  }

})

const closeModal = (() => {
  props.currentStatus.status = status.value.pending
  isLoading.value = false
  showModal.value = false
})

</script>


