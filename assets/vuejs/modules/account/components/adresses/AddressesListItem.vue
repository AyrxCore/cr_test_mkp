<template>
  <td class="p-5">{{ props.address.id }}</td>
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
const companyStore = useCompanyStore()

const props = defineProps({
  address: {
    required: true,
    type: Object,
  },
})

const onEditAddressClick = async () => {
  await companyStore.setCurrentAddress(props.address)
  router.push({
    name: AccountPageList.ADDRESS_EDIT,
    params: {id: props.address.id}
  })
}

</script>
