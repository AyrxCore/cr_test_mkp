<template>
  <div>
    <LoaderSharedComponent v-if="isLoadingDownload" />
    <button
      v-else
      class="rounded-lg border border-primary p-0.5"
      title="Télécharger la facture"
      @click="downloadInvoice"
    >
      <DownloadIconComponent class="h-[18px] w-[18px] stroke-primary" />
    </button>
  </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue'
import { Invoice } from '@/vuejs/types/Order'
import { hexToBinary } from '@/vuejs/services/utils'
import DownloadIconComponent from '@/vuejs/modules/shared/icon/DownloadIconComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { useOrderStore } from '@/vuejs/stores/order'

const orderStore = useOrderStore()
const isLoadingDownload = ref<boolean>(false)

const props = defineProps({
  paymentId: {
    required: true,
    type: Number,
  },
})

const emit = defineEmits(['downloadInvoice'])

const downloadInvoice = async () => {
  isLoadingDownload.value = true
  const invoice = <Invoice>await orderStore.getOrderInvoiceById(props.paymentId)
  const fileContentEncoded = invoice.content

  // Convertir les octets hexadécimaux en octets binaires
  const fileContentBinary = hexToBinary(fileContentEncoded)

  // Créer un objet Blob à partir de la chaîne binaire
  const blob = new Blob([fileContentBinary], { type: 'application/pdf' })

  // Créer un lien temporaire pour télécharger le fichier
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = invoice.name

  // Cliquez sur le lien pour déclencher le téléchargement
  link.click()

  isLoadingDownload.value = false
}
</script>
