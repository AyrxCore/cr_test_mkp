<template>
  <div class="modal-overlay">
    <div
      v-if="modelValue"
      v-click-outside="closeMenu"
      class="!lg:h-auto !sm:w-4/5 absolute top-0 right-0 z-10 flex flex-col overflow-auto rounded bg-white bg-secondary p-3 px-5 py-2.5 text-sm text-black shadow sm:top-24 sm:right-32 sm:h-fit sm:rounded md:p-5 lg:mx-0"
    >
      <div class="flex items-center">
        <RouterLink
          :to="{ name: AccountPageList.ACCOUNT }"
          class="flex items-center py-2.5 font-bold hover:text-secondary"
          @click="sendGtmEvent('click_header_mon_compte')"
        >
          <UserIcon :icon-color="channelPrimaryColor" class="mr-3" />
          <span>Mon compte</span>
        </RouterLink>
        <CloseIcon
          class="ml-auto cursor-pointer fill-primary stroke-primary hover:text-secondary"
          @click.stop="closeMenu"
        />
      </div>
      <hr class="my-2.5" />
      <RouterLink
        v-for="(value, key) in listAccount"
        :key="key"
        :to="{ name: value.routeName }"
        class="flex items-center py-2.5 hover:text-secondary"
        @click="sendGtmEvent(value.gtmEventName)"
      >
        <ChevronRightIcon
          class="mr-4 fill-primary stroke-primary hover:stroke-secondary"
        />
        {{ value.label }}
      </RouterLink>
      <div
        v-if="user.account?.adherent && user.account?.adherent.reducceCode"
        class="mt1 flex items-center py-2.5"
      >
        <!--      <ChevronRightIcon class="mr-4"/>-->
        Code Bonuus {{ user.account.adherent.reducceCode }}
      </div>
      <a
        href="#"
        class="inline-flex pt-5 font-bold hover:text-secondary"
        @click="onLogout($event)"
      >
        <DisconnectIcon :icon-color="channelPrimaryColor" class="mr-2" />
        Se déconnecter
      </a>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'

import CloseIcon from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'
import UserIcon from '@/vuejs/modules/shared/icon/UserIconComponent.vue'
import DisconnectIcon from '@/vuejs/modules/shared/icon/DisconnectIconComponent.vue'

import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'
import { AccountPageList } from '@/vuejs/router/pages-list'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'
import ChevronRightIcon from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'

const emit = defineEmits(['update:modelValue'])

const { channelPrimaryColor } = storeToRefs(useChannelStore())

const props = defineProps({
  modelValue: {
    required: true,
    type: Boolean,
  },
})

const listAccountGlobal = ref<any[]>([
  {
    label: 'Historique des commandes',
    routeName: AccountPageList.ORDERS,
    gtmEventName: 'click_header_mes_commandes',
  },
  {
    label: 'Mes coordonnées',
    routeName: AccountPageList.ACCOUNT,
  },
  {
    label: 'Listes de produits favoris',
    routeName: AccountPageList.FAVORITES_LIST,
    condition: OPTIONAL_FRONT_BLOCKS.FAVORITES,
  },
  {
    label: 'Paniers sauvegardés',
    routeName: AccountPageList.SAVED_CARTS,
  },
])

const listAccount = computed(() => {
  return listAccountGlobal.value.filter(
    (x) =>
      !x.condition ||
      (x.condition && channelStore.isAllowedToShow(x.condition)),
  )
})

const userStore = useUserStore()
const { user } = storeToRefs(userStore)
const channelStore = useChannelStore()

const currentChannel = channelStore.currentChannel

const closeMenu = (): void => {
  emit('update:modelValue', false)
}

const onLogout = async (e: Event): Promise<void> => {
  e.preventDefault()
  ;(await userStore.logout()) && location.reload()
  sendGtmEvent('click_header_log_out')
}
</script>
