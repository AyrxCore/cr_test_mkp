<template>
  <td class="p-5 flex justify-center">
    <div v-if="props.address.id !== selectedAddress">
      <a
          href="#"
          @click="onDefaultAdressSelect($event)"
      >
        <StarIconComponent
            stroke-color="purple"

        />
      </a>
    </div>
    <div v-else>
      <StarIconComponent
          fill-color="purple"
          classes="w-8 h-8"
      />
    </div>
    <div v-if="isItemLoading">
      <LoaderSharedComponent
        class="text-purple-600"
      />
    </div>
  </td>
  <td class="p-5">{{ props.address.company }}</td>
  <td class="p-5">{{ props.address.street }}</td>
  <td class="p-5">{{ props.address.postcode }}</td>
  <td class="p-5">{{ props.address.city }}</td>
  <td>
    <div v-if="!props.address.default" class="flex">
      <button
        @click="onEditAddressClick"
      >
        <EditIconComponent class="mr-2" />
      </button>
    </div>
  </td>
</template>
<script lang="ts" setup>
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import {useCompanyStore} from '@/vuejs/stores/company'
import router from '@/vuejs/router'
import {AccountPageList} from '@/vuejs/modules/account/routerAccount'
import StarIconComponent from '@/vuejs/modules/shared/icon/StarIconComponent.vue'
import {useUserStore} from '@/vuejs/stores/user'
import {computed, ref} from 'vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

const userStore = useUserStore()
const companyStore = useCompanyStore()
const isItemLoading = ref<boolean>(false)

const props = defineProps({
  address: {
    required: true,
    type: Object,
  },
  type: {
    required: true,
    type: String,
  }
})

const selectedAddress = computed(():number => {
  return props.type === 'billing'
    ? userStore.user.account.subaccount.billing_address
    : userStore.user.account.subaccount.shipping_address
})

const emit = defineEmits<{
  (eventName: 'click',value: number): void
}>()

const onEditAddressClick = async () => {
  await companyStore.setCurrentAddress(props.address)
  router.push({
    name: AccountPageList.ADDRESS_EDIT,
    params: {id: props.address.id}
  })
}


const onDefaultAdressSelect = async (e: Event) => {
  e.preventDefault()
  isItemLoading.value = true
  if(props.type === 'billing') {
    await userStore.updateUserDefaultBillingAddress(props.address.id)
  } else if (props.type === 'shipping') {
    await userStore.updateUserDefaultShippingAddress(props.address.id)
  }
  isItemLoading.value = false
}

</script>
