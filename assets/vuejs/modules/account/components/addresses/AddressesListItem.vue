<template>
  <td class="hidden p-3 pl-6 md:table-cell">{{ props.address.fullName }}</td>
  <td class="max-w-xs p-3 pt-6">
    <span class="flex">{{ props.address.address }}</span>
    <span class="flex"
      >{{ props.address.zipcode }} {{ props.address.city }}</span
    >
  </td>
  <td>
    <div class="flex items-center justify-end">
      <!-- TODO: Réactiver après le go-live quand la fonctionnalité adresse par défaut (favori) sera disponible côté Djust
      <div v-if="isItemLoading">
        <LoaderSharedComponent class="text-secondary" />
      </div>
      <a
        v-if="props.address.id !== selectedAddress"
        :class="{
          disabled: isNeoAutoLogin,
        }"
        class="mr-2 px-2 text-sm text-secondary md:text-base"
        href="#"
        title="Définir cette adresse par défaut"
        @click="onDefaultAdressSelect($event)"
      >
        <StarIconComponent class="stroke-primary" />
      </a>
      <label v-else class="mr-2 rounded px-2" title="Adresse par défaut">
        <StarIconComponent class="fill-primary stroke-primary" />
      </label>
      -->
      <button :disabled="isNeoAutoLogin" @click="onEditAddressClick">
        <EditIconComponent :stroke="channelPrimaryColor" class="mr-6" />
      </button>
    </div>
  </td>
</template>

<script lang="ts" setup>
import { PropType } from 'vue'
import { storeToRefs } from 'pinia'

import router from '@/vuejs/router'
import { AccountPageList } from '@/vuejs/router/pages-list'
import { useUserStore } from '@/vuejs/stores/user'
import { useAddressStore } from '@/vuejs/stores/address'
import { useChannelStore } from '@/vuejs/stores/channel'
import { Address } from '@/vuejs/types/Address'

import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
// TODO: Réactiver après le go-live (adresse par défaut / favori)
// import { computed, ref } from 'vue'
// import { ADDRESS_BILLING, ADDRESS_SHIPPING } from '@/vuejs/services/const'
// import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
// import StarIconComponent from '@/vuejs/modules/shared/icon/StarIconComponent.vue'

const addressStore = useAddressStore()

const props = defineProps({
  address: {
    required: true,
    type: Object as PropType<Address>,
  },
  type: {
    required: true,
    type: String,
  },
})

const { isNeoAutoLogin } = storeToRefs(useUserStore())
const { channelPrimaryColor } = storeToRefs(useChannelStore())

// TODO: Réactiver après le go-live (adresse par défaut / favori)
// const isItemLoading = ref<boolean>(false)
//
// const selectedAddress = computed((): string => {
//   return props.type === ADDRESS_BILLING
//     ? userStore.user.externalApiData?.subaccount?.billing_address
//     : userStore.user.externalApiData?.subaccount?.shipping_address
// })
//
// const onDefaultAdressSelect = async (e: Event) => {
//   e.preventDefault()
//   isItemLoading.value = true
//   if (props.type === ADDRESS_BILLING) {
//     await userStore.updateUserDefaultBillingAddress(props.address.id)
//   } else if (props.type === ADDRESS_SHIPPING) {
//     await userStore.updateUserDefaultShippingAddress(props.address.id)
//   }
//   isItemLoading.value = false
// }

const onEditAddressClick = async () => {
  await addressStore.setCurrentAddress(props.address)
  router.push({
    name: AccountPageList.ADDRESS_EDIT,
    params: { id: props.address.id },
  })
}
</script>
