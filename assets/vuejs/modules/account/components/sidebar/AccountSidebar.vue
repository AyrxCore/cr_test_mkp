<template>
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
