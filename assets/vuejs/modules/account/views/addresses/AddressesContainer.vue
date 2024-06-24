<template>
  <AccountPage>
    <template #right-side>
      <div class="flex justify-between">
        <div>
          <h3 class="text-title-primary mb-6 mt-2 xl:mt-0">Adresses</h3>
        </div>
      </div>
      <AddressesDefault :address="addressStore.defaultShippingAddressFormatted">
        <template #title> Votre adresse de livraison par défaut</template>
      </AddressesDefault>
      <AddressesDefault :address="addressStore.defaultBillingAddressFormatted">
        <template #title> Votre adresse de facturation par défaut</template>
      </AddressesDefault>
      <div class="mb-4 flex items-center justify-between">
        <div>
          <h3 class="ml-4 text-2xl font-bold text-primary">
            Adresses de livraison
          </h3>
        </div>
        <div>
          <ButtonComponent
            :disabled="isNeoAutoLogin"
            class="button-primary !hidden md:!inline-flex"
            @click="onCreateAddressClick(ADDRESS_SHIPPING)"
          >
            <AddIconComponent class="!mr-0 flex w-[20px] md:hidden" />
            <span class="hidden md:flex">Ajouter une adresse de livraison</span>
          </ButtonComponent>
          <div
            :class="{
              disabled: isNeoAutoLogin,
            }"
            class="rounded-full border border-primary px-2 py-1 md:hidden"
            @click="onCreateAddressClick(ADDRESS_SHIPPING)"
          >
            <AddIconComponent
              :fill="channelPrimaryColor"
              :stroke="channelPrimaryColor"
              class="!mr-0 flex w-[20px]"
            />
          </div>
        </div>
      </div>
      <div class="w-full">
        <AddressesList type="shipping" />
      </div>
      <div class="mb-4 mt-4 flex justify-between md:mt-8">
        <div>
          <h3 class="ml-4 text-2xl font-bold text-primary">
            Adresses de facturation
          </h3>
        </div>
        <div>
          <ButtonComponent
            :disabled="isNeoAutoLogin"
            class="button-primary !hidden md:!inline-flex"
            @click="onCreateAddressClick(ADDRESS_BILLING)"
          >
            <AddIconComponent class="!mr-0 flex w-[20px] md:hidden" />
            <span class="hidden md:flex"
              >Ajouter une adresse de facturation</span
            >
          </ButtonComponent>
          <div
            :class="{
              disabled: isNeoAutoLogin,
            }"
            class="rounded-full border border-primary px-2 py-1 md:hidden"
            @click="onCreateAddressClick(ADDRESS_BILLING)"
          >
            <AddIconComponent
              :fill="channelPrimaryColor"
              :stroke="channelPrimaryColor"
              class="!mr-0 flex w-[20px]"
            />
          </div>
        </div>
      </div>
      <div class="w-full">
        <AddressesList type="billing" />
      </div>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import { onMounted } from 'vue'
import { storeToRefs } from 'pinia'

import router from '@/vuejs/router'
import { AccountPageList } from '@/vuejs/router/pages-list'
import { ADDRESS_BILLING, ADDRESS_SHIPPING } from '@/vuejs/services/const'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import { useAddressStore } from '@/vuejs/stores/address'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useUserStore } from '@/vuejs/stores/user'

import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AddressesList from '@/vuejs/modules/account/components/addresses/AddressesList.vue'
import AddressesDefault from '@/vuejs/modules/account/components/addresses/AddressesDefault.vue'
import AddIconComponent from '@/vuejs/modules/shared/icon/AddIconComponent.vue'

const addressStore = useAddressStore()
const { channelPrimaryColor } = storeToRefs(useChannelStore())
const { isNeoAutoLogin } = storeToRefs(useUserStore())

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
  const gaEventName =
    type === ADDRESS_BILLING
      ? 'click_adresse_add_billing'
      : 'click_adresse_add_shipping'
  sendGaEvent(gaEventName)
}
</script>
