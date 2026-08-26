<template>
  <table class="list-address w-full table-auto">
    <AddressesListHeader />
    <tbody v-if="!isLoading" class="bg-white">
      <tr v-for="(address, key) in listAdresses" :key="key">
        <AddressesListItem :address="address" :type="props.type" />
      </tr>
    </tbody>
    <tbody v-else>
      <tr>
        <td class="mx-auto pb-4 pt-2" colspan="6">
          <LoaderSharedComponent
            class="mx-auto text-secondary"
            classes="loader-xl loader"
          />
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'

import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { useAddressStore } from '@/vuejs/stores/address'
import { Address } from '@/vuejs/types/Address'

import AddressesListItem from '@/vuejs/modules/account/components/addresses/AddressesListItem.vue'
import AddressesListHeader from '@/vuejs/modules/account/components/addresses/AddressesListHeader.vue'

const addressStore = useAddressStore()

const { addresses, isLoading } = storeToRefs(addressStore)
const props = defineProps({
  type: {
    required: true,
    type: String,
  },
})

const listAdresses = computed(() => {
  return addresses.value.filter((address: Address) =>
    props.type === 'shipping' ? address.shipping : address.billing,
  )
})
</script>

<style scoped>
@reference '@/style/main.css';

.list-address {
  th,
  td {
    @apply p-2 md:p-4;
  }
}
</style>
