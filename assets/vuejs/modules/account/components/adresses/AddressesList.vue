<template>
  <table class="table-auto w-full">
    <AddressesListHeader/>
    <tbody v-if="!isloading">
      <tr v-for="(address, key) in adresses" :key="key">
        <AddressesListItem
          :address="address"
        />
      </tr>
    </tbody>
    <tbody v-else>
      <tr>
        <td colspan="6">
          <LoaderSharedComponent/>
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
const companyStore = useCompanyStore()
const {adresses, isloading} = storeToRefs(companyStore)
</script>
