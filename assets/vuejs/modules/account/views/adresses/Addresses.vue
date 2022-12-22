<template>
  <AccountPage>
    <template #right-side>
      <div class="flex justify-between">
        <div>
          <h3 class="primary mb-2 text-[35px]">Adresses</h3>
        </div>
        <div>
          <ButtonComponent
            class="default-button mr-2 mb-2 flex items-center px-4 py-5 text-sm font-medium bg-transparent
             !text-purple-500 rounded-full border border-purple-600"
            @click="onCreateAddressClick"
          >
            Ajouter une adresse
          </ButtonComponent>
        </div>
      </div>
      <AddressesDefaultBilling/>
      <AddressesDefaultShipping/>
      <h3 class="primary mb-2 text-[25px]">Autres adresses</h3>
      <div class="py-3 px-3 grid bg-white rounded-lg flex text-gray-500 mb-8 ">
        <AddressesListFilters/>
      </div>
      <div class="w-full bg-white">
        <AddressesList/>
      </div>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>

import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { onMounted} from 'vue'
import {useCompanyStore} from '@/vuejs/stores/company'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AddressesList from '@/vuejs/modules/account/components/adresses/AddressesList.vue'
import AddressesDefaultBilling from '@/vuejs/modules/account/components/adresses/AddressesDefaultBilling.vue'
import AddressesDefaultShipping from '@/vuejs/modules/account/components/adresses/AddressesDefaultShipping.vue'
import AddressesListFilters from '@/vuejs/modules/account/components/adresses/AddressesListFilters.vue'
import router from '@/vuejs/router'
import {AccountPageList} from '@/vuejs/modules/account/routerAccount'
const companyStore = useCompanyStore()


onMounted(async () => {
  companyStore.isloading = !companyStore.adresses.length
  await companyStore.getAdresses()
  companyStore.isloading = false
})

const onCreateAddressClick = () => {
  companyStore.initNewAddress()
  router.push({
    name: AccountPageList.ADDRESS_CREATE
  })
}


</script>

<style scoped></style>
