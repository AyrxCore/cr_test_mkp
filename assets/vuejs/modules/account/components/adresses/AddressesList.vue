<template>
  <table class="table-auto w-full list-address">
    <AddressesListHeader/>
    <tbody v-if="!isloading">
      <tr
          v-for="(address, key) in adresses.filter((address) => address.type === props.type)"
          :key="key"
      >
        <AddressesListItem
          :address="address"
          :type="props.type"
        />
      </tr>
    </tbody>
    <tbody v-else>
      <tr>
        <td
            class="mx-auto pt-2 pb-4"
            colspan="6"
        >
          <LoaderSharedComponent
            class="text-purple-600 mx-auto"
            classes="loader-xl loader"
          />
        </td>
      </tr>
    </tbody>
  </table>
</template>
<script lang="ts" setup>
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import {storeToRefs} from 'pinia'
import {useBuyerCompanyStore} from '@/vuejs/stores/buyer_company'
import AddressesListItem from '@/vuejs/modules/account/components/adresses/AddressesListItem.vue'
import AddressesListHeader from '@/vuejs/modules/account/components/adresses/AddressesListHeader.vue'
const props = defineProps({
  type: {
    required: true,
    type: String,
  }
})

const buyerCompanyStore = useBuyerCompanyStore()
const {adresses, isloading} = storeToRefs(buyerCompanyStore)

</script>

<style scoped>
.list-address {
  th,
  td {
    @apply p-2 md:p-4;
  }
}
</style>
