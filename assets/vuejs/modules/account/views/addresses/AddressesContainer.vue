<template>
  <AccountPage>
    <template #right-side>
      <div class="flex justify-between">
        <div>
          <h3 class="mb-2 mt-2 text-title-35 text-primary md:mt-0">Adresses</h3>
        </div>
      </div>
      <AddressesDefault
        :address="buyerCompanyStore.defaultBillingAddressFormatted"
      >
        <template #title> Votre adresse de facturation par défaut </template>
      </AddressesDefault>
      <AddressesDefault
        :address="buyerCompanyStore.defaultShippingAddressFormatted"
      >
        <template #title> Votre adresse de livraison par défaut </template>
      </AddressesDefault>
      <div class="mb-4 flex justify-between md:mb-8">
        <div>
          <h3 class="mb-2 text-[19px] text-primary sm:text-[25px]">
            Adresses de livraison
          </h3>
        </div>
        <div>
          <ButtonComponent
            class="default-button btn-address"
            @click="onCreateAddressClick('shipping')"
          >
            <AddIconComponent
              class="!mr-0 flex w-[20px] text-primary md:hidden"
            />
            <span class="hidden md:flex">Ajouter une adresse de livraison</span>
          </ButtonComponent>
        </div>
      </div>
      <div class="w-full rounded bg-white">
        <AddressesList type="shipping" />
      </div>
      <div class="mb-4 mt-4 flex justify-between md:mb-8 md:mt-8">
        <div>
          <h3 class="text-[19px] text-primary sm:text-[25px]">
            Adresses de facturation
          </h3>
        </div>
        <div>
          <ButtonComponent
            class="default-button btn-address"
            @click="onCreateAddressClick('billing')"
          >
            <AddIconComponent
              class="!mr-0 flex w-[20px] text-primary md:hidden"
            />
            <span class="hidden md:flex"
              >Ajouter une adresse de facturation</span
            >
          </ButtonComponent>
        </div>
      </div>
      <div class="w-full rounded bg-white">
        <AddressesList type="billing" />
      </div>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { onMounted } from 'vue'
import { useBuyerCompanyStore } from '@/vuejs/stores/buyer_company'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AddressesList from '@/vuejs/modules/account/components/addresses/AddressesList.vue'
import AddressesDefault from '@/vuejs/modules/account/components/addresses/AddressesDefault.vue'
import router from '@/vuejs/router'
import { AccountPageList } from '@/vuejs/router/pages-list'
import AddIconComponent from '@/vuejs/modules/shared/icon/AddIconComponent.vue'
const buyerCompanyStore = useBuyerCompanyStore()

onMounted(async () => {
  buyerCompanyStore.isloading = !buyerCompanyStore.addresses.length
  await buyerCompanyStore.getAddresses()
  buyerCompanyStore.isloading = false
})

const onCreateAddressClick = (type: string) => {
  router.push({
    name: AccountPageList.ADDRESS_CREATE,
    params: { type },
  })
}
</script>

<style scoped lang="postcss">
.btn-address {
  @apply mr-2 mb-2 flex !h-8 items-center rounded-full border border-purple-600 bg-transparent !px-1.5 !py-1 text-sm font-medium !text-purple-500 hover:!text-white md:!h-12 md:px-4 md:py-5;
}
</style>
