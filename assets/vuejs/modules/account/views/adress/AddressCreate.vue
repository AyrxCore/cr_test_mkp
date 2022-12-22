<template>
  <AccountPage>
    <template #right-side>
      <h3 class="primary mb-2 text-[35px]">Créer une adresse</h3>
      <AddressForm/>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AddressForm from '@/vuejs/modules/account/components/adress/AddressForm.vue'
import {useHead} from '@vueuse/head'
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'

const title = 'Créer une adresse'

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
