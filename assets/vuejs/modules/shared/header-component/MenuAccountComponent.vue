<template>
  <div class="modal-overlay">
    <div
      v-if="modelValue"
      v-click-outside="closeMenu"
      class="!lg:h-auto !sm:w-4/5 absolute right-0 top-0 z-10 flex flex-col overflow-auto rounded bg-secondary bg-white p-3 px-5 py-2.5 text-sm text-black shadow sm:right-32 sm:top-24 sm:h-fit sm:rounded md:p-5 lg:mx-0"
    >
      <div class="flex items-center">
        <RouterLink
          :to="{ name: AccountPageList.ACCOUNT }"
          class="flex items-center py-2.5 font-bold hover:text-secondary"
          @click="
            sendGtmEvent('account_click', {
              link_text: $event.target.innerText,
              link_url: router.resolve({
                name: AccountPageList.ACCOUNT,
              }).fullPath,
              origin_url: router.currentRoute.value.fullPath,
            })
          "
        >
          <UserIcon :stroke="channelPrimaryColor" class="mr-3" />
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
        @click="
          sendGtmEvent('account_click', {
            link_text: value.label,
            link_url: router.resolve({
              name: value.routeName,
            }).fullPath,
            origin_url: router.currentRoute.value.fullPath,
          })
        "
      >
        <ChevronRightIcon
          class="mr-4 fill-primary stroke-primary hover:stroke-secondary"
        />
        {{ value.label }}
      </RouterLink>
      <a
        class="inline-flex items-center pt-5 font-bold hover:text-secondary"
        href="#"
        @click="onLogout($event)"
      >
        <DisconnectIcon :stroke="channelPrimaryColor" class="mr-2" />
        Se déconnecter
      </a>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'

import router from '@/vuejs/router'
import { AccountPageList } from '@/vuejs/router/pages-list'
import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'

import CloseIcon from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'
import UserIcon from '@/vuejs/modules/shared/icon/UserIconComponent.vue'
import DisconnectIcon from '@/vuejs/modules/shared/icon/DisconnectIconComponent.vue'
import ChevronRightIcon from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'

const props = defineProps({
  modelValue: {
    required: true,
    type: Boolean,
  },
})

const { channelPrimaryColor } = storeToRefs(useChannelStore())
const userStore = useUserStore()
const channelStore = useChannelStore()

const emit = defineEmits(['update:modelValue'])

const listAccountGlobal = ref<any[]>([
  {
    label: 'Mes commandes',
    routeName: AccountPageList.ORDERS,
  },
  {
    label: 'Mes coordonnées',
    routeName: AccountPageList.ACCOUNT,
  },
  {
    label: 'Mes favoris',
    routeName: AccountPageList.FAVORITES_LIST,
    condition: OPTIONAL_FRONT_BLOCKS.FAVORITES,
  },
  {
    label: 'Mes paniers sauvegardés',
    routeName: AccountPageList.SAVED_CARTS,
    condition: OPTIONAL_FRONT_BLOCKS.SAVED_CARTS,
  },
])

const listAccount = computed(() => {
  return listAccountGlobal.value.filter(
    (x) => !x.condition || channelStore.isAllowedToShow(x.condition),
  )
})

const closeMenu = (): void => {
  emit('update:modelValue', false)
}

const onLogout = async (e: Event): Promise<void> => {
  e.preventDefault()
  await userStore.logout()
  location.reload()
}
</script>
