<template>
  <AccountSidebarBlock :items="listOrdersAndData" :title="'Ma consommation'" />
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
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'

import { AccountPageList } from '@/vuejs/router/pages-list'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useUserStore } from '@/vuejs/stores/user.ts'
// TODO (MKP-1411): Import à décommenter quand les fonctionnalités Favoris/Paniers seront disponibles via DJUST
// import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'

import AccountSidebarBlock from '@/vuejs/modules/account/components/sidebar/AccountSidebarBlock.vue'

const channelStore = useChannelStore()
const { hasNoAdherentData } = storeToRefs(useUserStore())

type SidebarItem = {
  name: string
  id: AccountPageList
  url: AccountPageList
  condition?: string
  gaEventName?: string
}

const listProfilMenuGlobal = ref<SidebarItem[]>([
  {
    name: 'Mes coordonnées',
    id: AccountPageList.ACCOUNT,
    url: AccountPageList.ACCOUNT,
    gaEventName: 'click_account_contact_detail',
  },
  // TODO (MKP-1411): Mes favoris temporairement masqué - à rétablir quand la fonctionnalité sera disponible via DJUST
  // {
  //   name: 'Mes favoris',
  //   id: AccountPageList.FAVORITES_LIST,
  //   url: AccountPageList.FAVORITES_LIST,
  //   condition: OPTIONAL_FRONT_BLOCKS.FAVORITES,
  //   gaEventName: 'click_account_favorites',
  // },
  // TODO (MKP-1411): Mes paniers sauvegardés temporairement masqué - à rétablir quand la fonctionnalité sera disponible via DJUST
  // {
  //   name: 'Mes paniers sauvegardés',
  //   id: AccountPageList.SAVED_CARTS,
  //   url: AccountPageList.SAVED_CARTS,
  //   condition: OPTIONAL_FRONT_BLOCKS.SAVED_CARTS,
  //   gaEventName: 'click_account_saved_carts',
  // },
])

const listProfilMenu = computed((): SidebarItem[] => {
  return listProfilMenuGlobal.value.filter(
    (x) => !x.condition || channelStore.isAllowedToShow(x.condition),
  )
})

const listOrdersAndData = computed((): SidebarItem[] => {
  const data: SidebarItem[] = []
  const orderMenu = {
    name: 'Mes commandes',
    id: AccountPageList.ORDERS,
    url: AccountPageList.ORDERS,
  }
  const dataMenu = {
    name: 'Mes données de consommation',
    id: AccountPageList.DASHBOARD,
    url: AccountPageList.DASHBOARD,
  }

  data.push(orderMenu)

  if (!hasNoAdherentData.value) {
    data.push(dataMenu)
  }
  return data
})
</script>
