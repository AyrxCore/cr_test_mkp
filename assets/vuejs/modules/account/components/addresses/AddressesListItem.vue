<template>
  <td class="hidden p-3 pl-6 md:table-cell">{{ props.address.name }}</td>
  <td
    :class="{ 'italic text-gray-300': !props.address.company }"
    class="hidden p-3 md:table-cell"
  >
    {{ props.address.company ? props.address.company : 'Non renseignée' }}
  </td>
  <td class="max-w-xs p-3 pt-6">
    <span class="flex">{{ props.address.street }}</span>
    <span class="flex"
      >{{ props.address.postcode }} {{ props.address.city }}</span
    >
  </td>
  <td>
    <div class="flex items-center justify-end">
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
      <button :disabled="isNeoAutoLogin" @click="onEditAddressClick">
        <EditIconComponent :stroke="channelPrimaryColor" class="mr-6" />
      </button>
    </div>
  </td>
</template>

<script lang="ts" setup>
import { computed, PropType, ref } from 'vue'
import router from '@/vuejs/router'
import { storeToRefs } from 'pinia'
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import StarIconComponent from '@/vuejs/modules/shared/icon/StarIconComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { Address } from '@/vuejs/types/Address'
import { AccountPageList } from '@/vuejs/router/pages-list'
import { useUserStore } from '@/vuejs/stores/user'
import { useAddressStore } from '@/vuejs/stores/address'
import { useChannelStore } from '@/vuejs/stores/channel'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import { ADDRESS_BILLING, ADDRESS_SHIPPING } from '@/vuejs/services/const'

const userStore = useUserStore()
const addressStore = useAddressStore()
const isItemLoading = ref<boolean>(false)

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

const { isNeoAutoLogin } = storeToRefs(userStore)
const { channelPrimaryColor } = storeToRefs(useChannelStore())

const selectedAddress = computed((): number => {
  return props.type === ADDRESS_BILLING
    ? userStore.user.externalApiData.subaccount.billing_address
    : userStore.user.externalApiData.subaccount.shipping_address
})

const emit = defineEmits<{
  (eventName: 'click', value: number): void
}>()

const onEditAddressClick = async () => {
  await addressStore.setCurrentAddress(props.address)
  router.push({
    name: AccountPageList.ADDRESS_EDIT,
    params: { id: props.address.id },
  })
  const gaEventName =
    props.type === ADDRESS_BILLING
      ? 'click_adresse_edit_billing'
      : 'click_adresse_edit_shopping'
  sendGaEvent(gaEventName)
}

const onDefaultAdressSelect = async (e: Event) => {
  e.preventDefault()
  isItemLoading.value = true
  if (props.type === ADDRESS_BILLING) {
    await userStore.updateUserDefaultBillingAddress(props.address.id)
  } else if (props.type === ADDRESS_SHIPPING) {
    await userStore.updateUserDefaultShippingAddress(props.address.id)
  }
  isItemLoading.value = false
  const gaEventName =
    props.type === ADDRESS_BILLING
      ? 'click_adresse_default_billing'
      : 'click_adresse_default_shipping'
  sendGaEvent(gaEventName)
}
</script>
