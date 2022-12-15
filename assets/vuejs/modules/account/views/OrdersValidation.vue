<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary mb-2 text-[35px]">Historique de commandes</h3>
      <span class="flex text-[16px] text-gray-500">La commande la plus récente apparaît en premier</span>
      <div class="inline-flex w-5/6 mt-5">
        <input-button-component
          placeholder="Date début - date fin"
          class="h-[28px] mr-2"
        >
          <CalendarCheckIconComponent />
        </input-button-component>
        <select class="h-[28px] text-[14px] w-3/6 rounded-md text-gray-600 placeholder-gray-400 py-0 text-center mr-2" >
          <option>Etat de la commande</option>
        </select>
        <select class="h-[28px] text-[14px] w-3/6 rounded-md text-gray-600 placeholder-gray-400 py-0 text-center mr-2" >
          <option>Compte</option>
        </select>
        <select class="h-[28px] text-[14px] w-3/6 rounded-md text-gray-600 placeholder-gray-400 py-0 text-center" >
          <option>Acheteur</option>
        </select>
      </div>
      <div class="flex text-[16px] text-gray-500 py-2.5 mt-10">
        <div class="w-3/12">
          Date de la commande
        </div>
        <div class="w-4/12">
          Détails de la commande
        </div>
        <div class="w-2/12">
          Etat
        </div>
        <div class="w-3/12">
          Livraison
        </div>
        <div class="w-3/12">
          Total de la commande
        </div>
        <div class="w-1/12"></div>
      </div>
      <OrderHistoryComponent v-for="(order, key) in listOrders" :key="key" :order="order" />
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
        type: 'pending'
      },
      address: {
        name: 'QANTIS',
        street: '185 allée des Cyprès',
        postalCode: '69760',
        city: 'LIMONEST',
        country: 'FRANCE',
      },
      total: 'XX,XX'
    })
  }

  return orders
})

</script>

<style scoped></style>
