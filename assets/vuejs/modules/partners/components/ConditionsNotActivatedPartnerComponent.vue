<template>
  <div>
    <div>
      <ButtonComponent
        class="button-gradient mb-7"
        :is-loading="isLoading"
        @click="sendSubmission"
      >
        <ArrowRightIconComponent /> Bénéficiez des conditions
      </ButtonComponent>
      <ModalValidationBeneficePartnerModal
        v-if="showModal"
        class="modal"
        @cancel="closeModal"
      />
    </div>

    <div class="condition-beneficiaire">
      <p v-html="text" />
    </div>
  </div>
</template>
<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import AccordCadreHttpClient from '@/vuejs/services/httpclient/AccordCadreHttpClient'
import ModalValidationBeneficePartnerModal
  from '@/vuejs/modules/partners/components/ValidationBeneficePartnerModal.vue'
import { PropType, ref } from 'vue'
import { AccountAccordCadre } from '@/vuejs/types/AccordCadre'
import { status } from '@/vuejs/modules/partners/partner'

const showModal = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const props = defineProps({
  currentStatus: {
    type: Object as PropType<AccountAccordCadre>,
    required: true
  },
  text: {
    type: String,
    default: null
  },
  url: {
    type: String,
    default: null
  },
})

const sendSubmission = (async () => {
  isLoading.value = true
  try {
    await AccordCadreHttpClient.get().updateAccountAccordsCadresByParams(
      {
        id:props.currentStatus.id,
        accountId: props.currentStatus.accountId,
        accordCadreId: props.currentStatus.accordCadreId,
        updateAt: props.currentStatus.updatedAt.toDateString,
        status: status.value.pending,
    }
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


