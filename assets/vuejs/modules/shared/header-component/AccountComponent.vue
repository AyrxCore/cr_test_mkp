<template>
  <div
    :class="{
      'rounded-2xl border-4 border-solid border-primary px-2 py-2 md:py-0':
        isNeoAutoLogin,
    }"
    class="flex cursor-pointer items-center"
    @click.stop="toggleMenu"
  >
    <UserPlainIconComponent v-if="!isNeoAutoLogin" />
    <UserAutoLoginIconComponent v-else class="fill-primary stroke-primary" />
    <div class="hidden text-lg md:ml-3 md:block md:max-w-[250px]">
      <span v-if="!isNeoAutoLogin"
        >Bienvenue {{ user.firstName }} {{ user.lastName }}</span
      >
      <span v-else class="font-bold text-primary"
        >Connecté en tant que
        <div class="text-black">
          {{ user.firstName }} {{ user.lastName }}
        </div></span
      >
    </div>
    <ArrowDownIconComponent class="ml-2" />
  </div>
  <MenuAccountComponent
    v-if="isMenuOpen"
    v-model="isMenuOpen"
    class="modal-overlay"
  />
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { storeToRefs } from 'pinia'

import MenuAccountComponent from '@/vuejs/modules/shared/header-component/MenuAccountComponent.vue'
import UserPlainIconComponent from '@/vuejs/modules/shared/icon/UserPlainIconComponent.vue'
import ArrowDownIconComponent from '@/vuejs/modules/shared/icon/ArrowDownIconComponent.vue'
import UserAutoLoginIconComponent from '@/vuejs/modules/shared/icon/UserAutoLoginIconComponent.vue'

import { useUserStore } from '@/vuejs/stores/user'

import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const userStore = useUserStore()

const { isNeoAutoLogin } = storeToRefs(useUserStore())
const { user } = storeToRefs(userStore)

const isMenuOpen = ref<boolean>(false)

const toggleMenu = (): void => {
  isMenuOpen.value = !isMenuOpen.value
  sendGaEvent('click_header_account')
}
</script>
