<template>
  <div
    v-if="modelValue"
    v-click-outside="closeMenu"
    class="fixed top-0 left-0 z-50 h-screen w-full bg-white px-5 py-2.5 text-sm text-primary shadow sm:absolute sm:h-fit sm:w-auto sm:rounded"
  >
    <div class="flex items-center">
      <RouterLink
        :to="{ name: AccountPageList.ACCOUNT }"
        class="flex items-center py-2.5 font-bold hover:text-secondary"
      >
        <UserIcon class="mr-3" />
        <span>Mon compte</span>
      </RouterLink>
      <CloseIcon
        class="ml-auto cursor-pointer hover:text-secondary"
        @click.stop="closeMenu"
      />
    </div>
    <hr class="my-2.5" />
    <RouterLink
      v-for="(value, key) in listAccount"
      :key="key"
      :to="{ name: value.routeName }"
      class="flex items-center py-2.5 hover:text-secondary"
    >
      <ChevronRightIcon class="mr-4" />
      {{ value.label }}
    </RouterLink>

    <div
      v-if="user.account.adherent.reducceCode"
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
      <DisconnectIcon class="mr-2" />
      Se déconnecter
    </a>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'

import CloseIcon from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'
import UserIcon from '@/vuejs/modules/shared/icon/UserIconComponent.vue'
import DisconnectIcon from '@/vuejs/modules/shared/icon/DisconnectIconComponent.vue'
import ChevronRightIcon from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'

import { useUserStore } from '@/vuejs/stores/user'
import { storeToRefs } from 'pinia'
import { AccountPageList } from '@/vuejs/router/pages-list'

const emit = defineEmits(['update:modelValue'])

const props = defineProps({
  modelValue: {
    required: true,
    type: Boolean,
  },
})

const listAccount = ref<any[]>([
  // {
  //   label: 'Historique des commandes',
  //   url: '/app/account/orders-history',
  // },
  // {
  //   label: 'Mes factures',
  //   url: '/app/account/orders-history',
  // },
  // {
  //   label: 'Bons de livraison',
  //   url: '/app/account/orders-history',
  // },
  // {
  //   label: 'Validation de commande',
  //   url: '/app/account/orders-validation',
  // },
  {
    label: 'Mes coordonnées',
    routeName: AccountPageList.ACCOUNT,
  },
  // {
  //   label: 'Changer de SIRET',
  //   url: '/app/account/orders-history',
  // },
])

const userStore = useUserStore()
const { user } = storeToRefs(userStore)

const closeMenu = (): void => {
  emit('update:modelValue', false)
}

const onLogout = async (e: Event): Promise<void> => {
  e.preventDefault()
  ;(await userStore.logout()) && location.reload()
}
</script>
