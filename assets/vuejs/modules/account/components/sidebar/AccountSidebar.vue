<template>
  <AccountSidebarBlock
    :items="[
      // {
      //   name: 'Historiques de commandes',
      //   id: AccountPageList.ORDERS_HISTORY,
      //   url: AccountPageList.ORDERS_HISTORY,
      // },
      // { name: 'Factures', id: '', url: AccountPageList.ORDERS_HISTORY },
      {
        name: 'Paniers sauvegardés',
        id: AccountPageList.SAVED_CARTS,
        url: AccountPageList.SAVED_CARTS,
      },
      // {
      //   name: 'Bons de livraison',
      //   id: '',
      //   url: AccountPageList.ORDERS_HISTORY,
      // },
      // {
      //   name: 'Validation de commandes',
      //   id: AccountPageList.ORDERS_VALIDATION,
      //   url: AccountPageList.ORDERS_VALIDATION,
      // },
    ]"
    :title="'Mes Commandes'"
  />
  <AccountSidebarBlock
    :items="[
      {
        name: 'Coordonnées',
        id: AccountPageList.ACCOUNT,
        url: AccountPageList.ACCOUNT,
      },
      {
        name: 'Listes de produits favoris',
        id: AccountPageList.FAVORITES_LIST,
        url: AccountPageList.FAVORITES_LIST,
      },
    ]"
    title="Mon profil"
  />
  <AccountSidebarBlock
    :items="[
      {
        name: 'Adresses',
        id: AccountPageList.ADDRESSES,
        url: AccountPageList.ADDRESSES,
      },
    ]"
    title="Mon organisation"
  />
</template>
<script lang="ts" setup>
import { AccountPageList } from '@/vuejs/router/pages-list'
import AccountSidebarBlock from '@/vuejs/modules/account/components/sidebar/AccountSidebarBlock.vue'
</script>
