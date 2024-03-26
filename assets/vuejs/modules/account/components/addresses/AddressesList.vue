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
        <td class="mx-auto pt-2 pb-4" colspan="6">
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
import { storeToRefs } from 'pinia'

import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { useAddressStore } from '@/vuejs/stores/address'
import AddressesListItem from '@/vuejs/modules/account/components/addresses/AddressesListItem.vue'
import AddressesListHeader from '@/vuejs/modules/account/components/addresses/AddressesListHeader.vue'
import { computed, onBeforeMount } from 'vue'
import { Address } from '@/vuejs/types/Address'

const addressStore = useAddressStore()
onBeforeMount(() => {
  addressStore.getAddresses()
})

const { addresses, isLoading } = storeToRefs(addressStore)
const props = defineProps({
  type: {
    required: true,
    type: String,
  },
})

const listAdresses = computed(() => {
  return addresses.value.filter(
    (address: Address) => address.type === props.type,
  )
})
</script>

<style scoped lang="postcss">
.list-address {
  th,
  td {
    @apply p-2 md:p-4;
  }
}
</style>
