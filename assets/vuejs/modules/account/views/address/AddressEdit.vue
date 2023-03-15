<template>
  <AccountPage>
    <template #right-side>
      <h3 class="mt-2 mb-2 text-title-35 text-primary lg:mt-0">
        Editer une adresse
      </h3>
      <AddressForm />
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import { useHead } from '@vueuse/head'

import AddressForm from '@/vuejs/modules/account/components/address/AddressForm.vue'
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'

const title = 'Éditer une adresse'

useHead({
  title,
  meta: [
    {
      property: 'og:title',
      content: title,
    },
  ],
})
</script>
