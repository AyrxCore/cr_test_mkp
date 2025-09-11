<template>
  <AccountPage>
    <template #right-side>
      <h3 class="text-title-primary mb-6 mt-2 xl:mt-0">Editer une adresse</h3>
      <AddressForm />
    </template>
  </AccountPage>
</template>

<script lang="ts" setup>
import AddressForm from '@/vuejs/modules/account/components/address/AddressForm.vue'
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
</script>
