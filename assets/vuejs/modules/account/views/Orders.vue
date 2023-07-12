<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary my-2 text-title-35 lg:mb-2">
        Historiques de commandes
      </h3>
      <span class="flex text-sm text-gray-500 md:text-base">
        La commande la plus récente apparaît en premier
      </span>
      <div
        class="mt-5 hidden py-2.5 px-2.5 text-sm text-gray-500 md:flex lg:mt-10 lg:text-base"
      >
        <div class="md:w-2/12 lg:w-3/12">Date de la commande</div>
        <div class="md:w-5/12 lg:w-4/12">Détails de la commande</div>
        <div class="w-5/12">Livraison</div>
        <div class="w-3/12">Total de la commande</div>
        <div class="w-1/12"></div>
      </div>
      <LoadingComponent v-if="isLoading" />
      <div v-else-if="showAlert" class="lg:w-5/6">
        <AlertSharedComponent
          class="bg-red-200 text-red-800 dark:bg-red-200 dark:text-red-800"
        />
      </div>
      <div v-else>
        <div
          v-if="orderStore.orders.length === 0"
          class="mt-5 flex flex-row flex-wrap justify-center rounded-lg bg-white py-2 text-sm text-gray-600 md:text-base lg:text-lg"
        >
          Vous n'avez pas encore passé de commande
        </div>
        <orderComponent
          v-for="(order, key) in orderStore.orders"
          v-else
          :key="key"
          :order="order"
          @download-invoice="downloadInvoice"
        />
      </div>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { onMounted, ref } from 'vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import { useAlertStore } from '@/vuejs/stores/alert'
import { storeToRefs } from 'pinia'
import InformationIconComponent from '@/vuejs/modules/shared/icon/InformationIconComponent.vue'
import { useOrderStore } from '@/vuejs/stores/order'
import OrderComponent from '@/vuejs/modules/account/components/OrderComponent.vue'
import { Invoice } from '@/vuejs/types/Order'
import { hexToBinary } from '@/vuejs/services/utils'

const orderStore = useOrderStore()
const alertStore = useAlertStore()
const { show: showAlert } = storeToRefs(alertStore)
const isLoading = ref<boolean>(false)
const invoice = ref<Invoice>()

onMounted(async () => {
  isLoading.value = true
  try {
    await orderStore.getOrders()
  } catch (error) {
  } finally {
    isLoading.value = false
  }
})

const downloadInvoice = async (event) => {
  invoice.value = <Invoice>await orderStore.getOrderInvoiceById(event.paymentId)
  const fileContentEncoded = invoice.value.content
  // Convertir les octets hexadécimaux en octets binaires
  const fileContentBinary = hexToBinary(fileContentEncoded)
  // Créer un objet Blob à partir de la chaîne binaire
  const blob = new Blob([fileContentBinary], { type: 'application/pdf' })
  // Créer un lien temporaire pour télécharger le fichier
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = invoice.value.name
  // Cliquez sur le lien pour déclencher le téléchargement
  link.click()
}
</script>

<style scoped></style>
