<template>
  <AccountPage>
    <template #right-side>
      <h3 class="text-title-primary mt-2 mb-6 xl:mt-0">Editer une adresse</h3>
      <AddressForm
        @submit-address="sendGaEvent('click_edit_save')"
        @abort-cancel-address="sendGaEvent('click_edit_cancel')"
      />
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import { useHead } from '@unhead/vue'

import AddressForm from '@/vuejs/modules/account/components/address/AddressForm.vue'
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

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
