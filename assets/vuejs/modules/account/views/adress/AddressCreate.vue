<template>
  <AccountPage>
    <template #right-side>
      <h3 class="text-title-35 mt-2 mb-2 text-primary lg:mt-0">
        Créer une adresse de
        <span v-if="props.type === 'billing'"> facturation </span>
        <span v-else-if="props.type === 'shipping'"> livraison </span>
      </h3>
      <AddressForm :type="props.type" />
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AddressForm from '@/vuejs/modules/account/components/adress/AddressForm.vue'
import { useHead } from '@vueuse/head'
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'

const title = 'Créer une adresse'
const props = defineProps({
  type: {
    required: true,
    type: String,
  },
})

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
