<template>
  <td class="p-3">{{ props.address.name }}</td>
  <td
    class="hidden p-3 md:table-cell"
    :class="{ 'italic text-gray-300': !props.address.company }"
  >
    {{ props.address.company ? props.address.company : 'Non renseignée' }}
  </td>
  <td class="p-3 max-w-xs">
    <span class="flex">{{ props.address.street }}</span>
    <span class="flex">{{ props.address.postcode }}</span>
    <span class="flex">{{ props.address.city }}</span>
  </td>
  <td>
    <div class="flex items-center justify-end">
      <div v-if="isItemLoading">
        <LoaderSharedComponent class="text-purple-600" />
      </div>
      <a
        v-if="props.address.id !== selectedAddress"
        class="mr-2 px-2 text-sm text-secondary md:text-base"
        href="#"
        title="Définir cette adresse par défaut"
        @click="onDefaultAdressSelect($event)"
      >
        <StarIconComponent class="stroke-secondary" />
      </a>
      <label v-else class="mr-2 rounded px-2" title="Adresse par défaut">
        <StarIconComponent class="fill-secondary stroke-secondary" />
      </label>
      <button @click="onEditAddressClick">
        <EditIconComponent class="mr-2" />
      </button>
    </div>
  </td>
</template>
<script lang="ts" setup>
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import { useBuyerCompanyStore } from '@/vuejs/stores/buyer_company'
import router from '@/vuejs/router'
import { AccountPageList } from '@/vuejs/modules/account/routerAccount'
import StarIconComponent from '@/vuejs/modules/shared/icon/StarIconComponent.vue'
import { useUserStore } from '@/vuejs/stores/user'
import { computed, ref } from 'vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

const userStore = useUserStore()
const buyerCompanyStore = useBuyerCompanyStore()
const isItemLoading = ref<boolean>(false)

const props = defineProps({
  address: {
    required: true,
    type: Object,
  },
  type: {
    required: true,
    type: String,
  },
})

const selectedAddress = computed((): number => {
  return props.type === 'billing'
    ? userStore.user.account.subaccount.billing_address
    : userStore.user.account.subaccount.shipping_address
})

const emit = defineEmits<{
  (eventName: 'click', value: number): void
}>()

const onEditAddressClick = async () => {
  await buyerCompanyStore.setCurrentAddress(props.address)
  router.push({
    name: AccountPageList.ADDRESS_EDIT,
    params: { id: props.address.id },
  })
}

const onDefaultAdressSelect = async (e: Event) => {
  e.preventDefault()
  isItemLoading.value = true
  if (props.type === 'billing') {
    await userStore.updateUserDefaultBillingAddress(props.address.id)
  } else if (props.type === 'shipping') {
    await userStore.updateUserDefaultShippingAddress(props.address.id)
  }
  isItemLoading.value = false
}
</script>
