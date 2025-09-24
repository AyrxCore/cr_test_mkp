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
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'

import AccountSidebarBlock from '@/vuejs/modules/account/components/sidebar/AccountSidebarBlock.vue'

const channelStore = useChannelStore()
const { hasNoAdherentData } = storeToRefs(useUserStore())

const listProfilMenuGlobal = ref<any[]>([
  {
    name: 'Mes coordonnées',
    id: AccountPageList.ACCOUNT,
    url: AccountPageList.ACCOUNT,
    gaEventName: 'click_account_contact_detail',
  },
  {
    name: 'Mes favoris',
    id: AccountPageList.FAVORITES_LIST,
    url: AccountPageList.FAVORITES_LIST,
    condition: OPTIONAL_FRONT_BLOCKS.FAVORITES,
    gaEventName: 'click_account_favorites',
  },
  {
    name: 'Mes paniers sauvegardés',
    id: AccountPageList.SAVED_CARTS,
    url: AccountPageList.SAVED_CARTS,
    condition: OPTIONAL_FRONT_BLOCKS.SAVED_CARTS,
    gaEventName: 'click_account_saved_carts',
  },
])

const listProfilMenu = computed((): any[] => {
  return listProfilMenuGlobal.value.filter(
    (x) => !x.condition || channelStore.isAllowedToShow(x.condition),
  )
})

const listOrdersAndData = computed((): any[] => {
  const data = []
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
