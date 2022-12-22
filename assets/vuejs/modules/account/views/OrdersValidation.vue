<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary mb-2 page-principal-title mt-2 md:mt-0">Historique de commandes</h3>
      <span
        class="flex text-sm md:text-base lg:text-lg text-gray-500"
      >La commande la plus récente apparaît en premier</span
      >
      <div class="mt-5 mb-3 md:mb-0 flex flex-col md:flex-row w-full lg:w-11/12">
        <input-button-component
          placeholder="Date début - date fin"
          class="mr-2 h-[28px] w-full lg:w-4/12 text-sm md:text-base lg:text-lg mb-3 md:mb-0"
        >
          <CalendarCheckIconComponent />
        </input-button-component>
        <select
          class="mr-2 h-[28px] w-full lg:w-3/12 rounded-md py-0 md:text-center text-sm md:text-base lg:text-lg text-gray-600 placeholder-gray-400 mb-3 md:mb-0"
        >
          <option>Etat de la commande</option>
        </select>
        <select
          class="mr-2 h-[28px] w-full lg:w-3/12 rounded-md py-0 md:text-center text-sm md:text-base lg:text-lg text-gray-600 placeholder-gray-400 mb-3 md:mb-0"
        >
          <option>Compte</option>
        </select>
        <select
          class="h-[28px] w-full lg:w-3/12 rounded-md py-0 md:text-center text-sm md:text-base lg:text-lg text-gray-600 placeholder-gray-400 mb-3 md:mb-0"
        >
          <option>Acheteur</option>
        </select>
      </div>
      <div class="mt-10 mb-2.5 px-2.5 items-center text-sm lg:text-base text-gray-500 hidden md:flex">
        <div class="md:w-2/12 lg:w-3/12">Date de la commande</div>
        <div class="md:w-5/12 w-4/12">Détails de la commande</div>
        <div class="w-2/12">Etat</div>
        <div class="w-3/12">Livraison</div>
        <div class="w-3/12">Total de la commande</div>
        <div class="w-1/12"></div>
      </div>
      <OrderHistoryComponent
        v-for="(order, key) in listOrders"
        :key="key"
        :order="order"
      />
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { computed } from 'vue'
import { AccountPageList } from '@/vuejs/modules/account/routerAccount'
import OrderHistoryComponent from '@/vuejs/modules/account/components/OrderHistoryComponent.vue'
import InputButtonComponent from '@/vuejs/modules/shared/InputButtonComponent.vue'
import CalendarCheckIconComponent from '@/vuejs/modules/shared/icon/CalendarCheckIconComponent.vue'

const tab = computed(() => {
  return AccountPageList.ORDERS_VALIDATION
})

const listOrders = computed(() => {
  const orders = []
  const statuts = ['En attente', 'En attente', 'En attente']
  const colorStatuts = ['bg-red-600', 'bg-red-600', 'bg-red-600']
  const dateOrder = ['01/01/2022', '07/04/2022', '19/08/2022']

  for (let i = 0; i < 3; i++) {
    const rndNb = Math.floor(Math.random() * 6) + 1
    orders.push({
      date: dateOrder[i],
      name: 'XXXXX',
      reference: 'XXXXX',
      numberArticle: rndNb,
      sellerName: 'Nom Prénom',
      accountName: 'Nom du compte',
      statut: {
        name: statuts[i],
        color: colorStatuts[i],
        type: 'pending',
      },
      address: {
        name: 'QANTIS',
        street: '185 allée des Cyprès',
        postalCode: '69760',
        city: 'LIMONEST',
        country: 'FRANCE',
      },
      total: 'XX,XX',
    })
  }

  return orders
})
</script>

<style scoped></style>
