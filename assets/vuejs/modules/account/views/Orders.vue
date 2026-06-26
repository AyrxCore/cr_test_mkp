<template>
  <AccountPage>
    <template #right-side>
      <h3 class="text-title-primary my-2 lg:mb-2">Mes commandes</h3>
      <span class="mb-2 flex text-sm md:text-base">
        La commande la plus récente apparaît en premier
      </span>
      <div
        class="mt-5 hidden px-2.5 py-2.5 text-sm text-gray-500 md:flex lg:mt-10 lg:text-base"
      >
        <div class="md:w-2/12 lg:w-3/12">Date de la commande</div>
        <div class="md:w-5/12 lg:w-4/12">Détails de la commande</div>
        <div class="md:w-5/12">Livraison</div>
        <div class="md:w-3/12">Total de la commande</div>
        <div class="md:w-1/12"></div>
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
        <OrderComponent
          v-for="(order, key) in orderStore.orders"
          v-else
          :key="key"
          :order="order"
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
import { useOrderStore } from '@/vuejs/stores/order'
import OrderComponent from '@/vuejs/modules/account/components/OrderComponent.vue'

const orderStore = useOrderStore()
const alertStore = useAlertStore()
const { show: showAlert } = storeToRefs(alertStore)
const isLoading = ref<boolean>(false)

onMounted(async () => {
  isLoading.value = true
  try {
    await orderStore.getOrders()
  } catch (error) {
  } finally {
    isLoading.value = false
  }
})
</script>

<style scoped></style>
