<template>
  <AccountSidebarBlock
    :items="[
      {
        name: 'Mes commandes',
        id: AccountPageList.ORDERS,
        url: AccountPageList.ORDERS,
      },
    ]"
    :title="'Ma consommation'"
  />
  <AccountSidebarBlock :items="listProfilMenu" title="Mon profil" />
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
import { computed, ref } from 'vue'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'
import { useChannelStore } from '@/vuejs/stores/channel'

const channelStore = useChannelStore()

const listProfilMenuGlobal = ref<any[]>([
  {
    name: 'Mes coordonnées',
    id: AccountPageList.ACCOUNT,
    url: AccountPageList.ACCOUNT,
  },
  {
    name: 'Mes listes de produits favoris',
    id: AccountPageList.FAVORITES_LIST,
    url: AccountPageList.FAVORITES_LIST,
    condition: OPTIONAL_FRONT_BLOCKS.FAVORITES,
  },
  {
    name: 'Mes paniers sauvegardés',
    id: AccountPageList.SAVED_CARTS,
    url: AccountPageList.SAVED_CARTS,
    condition: OPTIONAL_FRONT_BLOCKS.SAVED_CARTS,
  },
])

const listProfilMenu = computed(() => {
  return listProfilMenuGlobal.value.filter(
    (x) => !x.condition || channelStore.isAllowedToShow(x.condition),
  )
})
</script>
