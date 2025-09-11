<template>
  <AccountPage>
    <template #right-side>
      <h3 class="text-title-primary mb-6 mt-2 xl:mt-0">
        Créer une adresse de
        <span v-if="props.type === ADDRESS_BILLING"> facturation </span>
        <span v-else-if="props.type === ADDRESS_SHIPPING"> livraison </span>
      </h3>
      <AddressForm :type="props.type" />
    </template>
  </AccountPage>
</template>

<script lang="ts" setup>
import { ADDRESS_BILLING, ADDRESS_SHIPPING } from '@/vuejs/services/const'

import AddressForm from '@/vuejs/modules/account/components/address/AddressForm.vue'
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'

const props = defineProps({
  type: {
    required: true,
    type: String,
  },
})
</script>
