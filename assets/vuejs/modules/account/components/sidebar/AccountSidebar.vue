<template>
  <AccountSidebarBlock
      :items="[
                  {name: 'Historiques de commandes',id: AccountPageList.ORDERS_HISTORY, url: AccountPageList.ORDERS_HISTORY},
                  {name: 'Factures',id: '', url:  AccountPageList.ORDERS_HISTORY},
                  {name: 'Paniers sauvegardés',id: AccountPageList.SAVED_CARTS, url:  AccountPageList.SAVED_CARTS},
                  {name: 'Bons de livraison', id: '',url:  AccountPageList.ORDERS_HISTORY},
                  {name: 'Validation de commandes',id: AccountPageList.ORDERS_VALIDATION,url:  AccountPageList.ORDERS_VALIDATION},
              ]"
      :title="'Mes Commandes'"
  />
  <AccountSidebarBlock
      :items="[
                  {name: 'Coordonnées',id: AccountPageList.CONTACT_INFORMATION, url: AccountPageList.CONTACT_INFORMATION},
                  {name: 'Liste de produits favoris',id: AccountPageList.FAVORIS_LIST, url: AccountPageList.FAVORIS_LIST},
              ]"
      title="Mon profil"
  />
  <AccountSidebarBlock
      :items="[
                  {name: 'Utilisateurs',id: '', url: AccountPageList.ACCOUNT},
                  {name: 'Adresses',id: AccountPageList.ADDRESSES, url: AccountPageList.ADDRESSES},
                  {name: 'Directions - circuits de validation',id: '', url: AccountPageList.ACCOUNT},
                  {name: 'Statuts',id: '', url: AccountPageList.ACCOUNT},
              ]"
      title="Mon organisation"
  />
  <AccountSidebarBlock
      :items="[
                  {name: 'Statistiques de consommation',id: '', url: AccountPageList.ACCOUNT},
              ]"
      title="Statistiques"
  />
</template>
<script lang="ts" setup>
import { AccountPageList } from '@/vuejs/modules/account/routerAccount'
import AccountSidebarBlock from '@/vuejs/modules/account/components/sidebar/AccountSidebarBlock.vue'
</script>
