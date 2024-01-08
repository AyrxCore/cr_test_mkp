<template>
  <AccountPage>
    <template #header>
      <h3 class="text-title-primary lg:mb-2">
        Bonjour, {{ user.firstName }}
        <span class="uppercase">{{ user.lastName }}</span>
      </h3>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { useUserStore } from '@/vuejs/stores/user'
import { storeToRefs } from 'pinia'

const userStore = useUserStore()
const { user } = storeToRefs(userStore)
</script>

<style scoped></style>
