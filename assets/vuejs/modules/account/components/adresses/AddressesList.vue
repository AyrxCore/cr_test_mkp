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
import {useCompanyStore} from '@/vuejs/stores/company'
import AddressesListItem from '@/vuejs/modules/account/components/adresses/AddressesListItem.vue'
import AddressesListHeader from '@/vuejs/modules/account/components/adresses/AddressesListHeader.vue'
import {useUserStore} from '@/vuejs/stores/user'
const props = defineProps({
  type: {
    required: true,
    type: String,
  }
})

const companyStore = useCompanyStore()
const userStore = useUserStore()
const {adresses, isloading} = storeToRefs(companyStore)

</script>

<style scoped>
.list-address {
  th,
  td {
    @apply p-2 md:p-4;
  }
}
</style>
