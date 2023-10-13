<template>
  <AccountPage>
    <template #right-side>
      <div class="flex justify-between">
        <div>
          <h3 class="mb-2 mt-2 text-title-35 text-primary md:mt-0">Adresses</h3>
        </div>
      </div>
      <AddressesDefault :address="addressStore.defaultBillingAddressFormatted">
        <template #title> Votre adresse de facturation par défaut</template>
      </AddressesDefault>
      <AddressesDefault :address="addressStore.defaultShippingAddressFormatted">
        <template #title> Votre adresse de livraison par défaut</template>
      </AddressesDefault>
      <div class="mb-4 flex justify-between md:mb-8">
        <div>
          <h3 class="mb-2 text-[19px] text-primary sm:text-[25px]">
            Adresses de livraison
          </h3>
        </div>
        <div>
          <ButtonComponent
            class="button-secondary-outline"
            @click="onCreateAddressClick(ADDRESS_SHIPPING)"
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
            class="button-secondary-outline"
            @click="onCreateAddressClick(ADDRESS_BILLING)"
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
import { useAddressStore } from '@/vuejs/stores/address'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AddressesList from '@/vuejs/modules/account/components/addresses/AddressesList.vue'
import AddressesDefault from '@/vuejs/modules/account/components/addresses/AddressesDefault.vue'
import router from '@/vuejs/router'
import { AccountPageList } from '@/vuejs/router/pages-list'
import AddIconComponent from '@/vuejs/modules/shared/icon/AddIconComponent.vue'
import { ADDRESS_BILLING, ADDRESS_SHIPPING } from '@/vuejs/services/const'

const addressStore = useAddressStore()

onMounted(async () => {
  addressStore.isLoading = !addressStore.addresses.length
  await addressStore.getAddresses()
  addressStore.isLoading = false
})

const onCreateAddressClick = (type: string) => {
  router.push({
    name: AccountPageList.ADDRESS_CREATE,
    params: { type },
  })
}
</script>
