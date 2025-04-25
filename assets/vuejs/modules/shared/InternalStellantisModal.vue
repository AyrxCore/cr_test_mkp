<template>
  <StellantisModal
    v-if="showStellantisModal"
    :is-loading="isLoading"
    @accept-stellantis="onAccept"
  />
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'

import ProductHttpClient from '@/vuejs/services/httpclient/ProductHttpClient'
import { useUserStore } from '@/vuejs/stores/user'
import { notifyError } from '@/vuejs/services/utils'

import StellantisModal from '@/vuejs/modules/login/component/StellantisModal.vue'

const userStore = useUserStore()

const hasAccepted = ref<boolean>(false)
const isLoading = ref<boolean>(false)

const showStellantisModal = computed<boolean>(() => {
  // TODO: Uncomment this when the modal is ready
  // const adherent = userStore.user?.account.adherent
  return false
  // return (
  //   adherent?.showModalStellantis &&
  //   !adherent?.stellantisModalValidated &&
  //   !hasAccepted.value
  // )
})

const onAccept = async (): Promise<void> => {
  try {
    isLoading.value = true
    // TODO: Uncomment this when the modal is ready
    // await ProductHttpClient.get().updateStellantisSubscription()
  } catch {
    notifyError('Veuillez contacter le support.')
  } finally {
    isLoading.value = false
    hasAccepted.value = true
  }
}
</script>
