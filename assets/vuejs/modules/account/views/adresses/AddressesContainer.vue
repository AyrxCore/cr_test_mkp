<template>
  <AccountPage>
    <template #right-side>
      <div class="flex justify-between">
        <div>
          <h3 class="page-principal-title mb-2 mt-2 text-primary md:mt-0">Adresses</h3>
        </div>
      </div>
      <AddressesDefaultBilling/>
      <AddressesDefaultShipping/>
      <div class="flex justify-between mb-8">
        <div>
          <h3 class="mb-2 text-[19px] text-primary sm:text-[25px]">
            Adresses de livraison
          </h3>
        </div>
        <div>
          <ButtonComponent
              class="default-button mr-2 mb-2 flex items-center px-4 py-5 text-sm font-medium bg-transparent
             !text-purple-500 rounded-full border border-purple-600"
              @click="onCreateAddressClick('shipping')"
          >
            Ajouter une adresse de livraison
          </ButtonComponent>
        </div>
      </div>
      <div class="w-full bg-white">
        <AddressesList
          type="shipping"
        />
      </div>
      <div class="flex justify-between mb-8 mt-8">
        <div>
          <h3 class="primary text-[25px]">Adresses de facturation</h3>
        </div>
        <div>
          <ButtonComponent
              class="default-button mr-2 mb-2 flex items-center px-4 py-5 text-sm font-medium bg-transparent
             !text-purple-500 rounded-full border border-purple-600"
              @click="onCreateAddressClick('billing')"
          >
            Ajouter une adresse de facturation
          </ButtonComponent>
        </div>
      </div>
      <div class="w-full bg-white">
        <AddressesList
          type="billing"
        />
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
import router from '@/vuejs/router'
import {AccountPageList} from '@/vuejs/modules/account/routerAccount'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
const companyStore = useCompanyStore()


onMounted(async () => {
  companyStore.isloading = !companyStore.adresses.length
  await companyStore.getAdresses()
  companyStore.isloading = false
})

const onCreateAddressClick = (type: string) => {
  router.push({
    name: AccountPageList.ADDRESS_CREATE,
    params: {type}
  })
}


</script>

<style scoped></style>
