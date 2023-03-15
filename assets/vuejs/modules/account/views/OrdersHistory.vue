<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary my-2 text-title-35 lg:mb-2">
        Historique de commandes
      </h3>
      <span class="flex text-sm text-gray-500 md:text-base">
        La commande la plus récente apparaît en premier
      </span>
      <span class="mt-3 mb-2 flex text-sm text-gray-500 md:text-base lg:mt-7"
        >Trier par</span
      >
      <div class="flex flex-col md:flex-row">
        <select
          class="h-[28px] w-full rounded-md py-0 text-center text-sm text-gray-600 placeholder-gray-400 md:mr-2 md:w-2/5 lg:w-3/12"
        >
          <option>Date par ordre décroissant</option>
        </select>
        <input-button-component
          placeholder="Numéro de commande / Nom de l'acheteur"
          class="mt-3 mb-3 h-[28px] w-full md:mb-0 md:mt-0 md:w-3/5 lg:w-7/12"
        >
          <SearchIconComponent />
        </input-button-component>
      </div>
      <div
        class="mt-5 hidden py-2.5 px-2.5 text-sm text-gray-500 md:flex lg:mt-10 lg:text-base"
      >
        <div class="md:w-2/12 lg:w-3/12">Date de la commande</div>
        <div class="md:w-5/12 lg:w-4/12">Détails de la commande</div>
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
import { AccountPageList } from '@/vuejs/router/pages-list'
import OrderHistoryComponent from '@/vuejs/modules/account/components/OrderHistoryComponent.vue'
import InputButtonComponent from '@/vuejs/modules/shared/InputButtonComponent.vue'
import SearchIconComponent from '@/vuejs/modules/shared/icon/SearchIconComponent.vue'

const tab = computed(() => {
  return AccountPageList.ORDERS_HISTORY
})

const listOrders = computed(() => {
  const orders = []
  const statuts = ['En cours', 'En attente', 'Livré']
  const colorStatuts = ['bg-qantis', 'bg-red-600', 'bg-green-500']
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
