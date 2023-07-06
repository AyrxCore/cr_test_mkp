<template>
  <div
    class="mb-5 flex flex-col rounded-lg bg-white text-sm text-gray-500 lg:text-[15px]"
  >
    <div class="mb-2.5 flex w-full flex-col px-5 py-2.5 md:flex-row md:px-2.5">
      <div class="flex w-full justify-start md:w-2/12 lg:w-3/12">
        <span class="flex text-gray-500"
          ><CalendarCheckIconComponent
            class="mr-1 h-[18px] w-[18px] stroke-gray-500"
          />
          {{ formatDateFr(order.createdAt) }}</span
        >
      </div>
      <div class="mt-5 w-full md:mt-0 md:w-5/12 lg:w-4/12">
        <span class="flex text-gray-500"
          >Commande : {{ order.orderNumber }}</span
        >
        <span class="flex text-gray-500">
          Articles : {{ order.items.length }}
        </span>

        <div
          class="flex items-center space-x-2 md:my-2 md:flex-col md:items-start md:justify-start md:space-x-0"
        >
          <span>Statut:</span>
          <span
            class="mt-1 w-fit rounded-md px-1 py-1 text-[14px] text-white"
            :class="ORDER_STATUS[order.state].color"
            :title="ORDER_STATUS[order.state].name"
            >{{ ORDER_STATUS[order.state].name }}</span
          >
        </div>
      </div>
      <div class="mt-5 w-full md:mt-0 md:w-5/12">
        <span class="md:hidden">Livraison</span>
        <div class="flex flex-col">
          <span class="flex text-gray-500">
            <MapInIconComponent class="mr-1 hidden stroke-gray-500 md:block" />
            {{ order.shippingAddress }}
          </span>
          <div class="my-2 flex flex-col md:pl-5">
            <div
              class="flex items-center space-x-2 md:flex-col md:items-start md:space-x-0"
            >
              <span>Statut:</span>
              <span
                class="mt-1 w-fit rounded-md px-1 py-1 text-[14px] text-white"
                :class="SHIPPING_STATUS[order.shippingState].color"
                :title="SHIPPING_STATUS[order.shippingState].name"
                >{{ SHIPPING_STATUS[order.shippingState].name }}</span
              >
            </div>

            <span class="mt-1">Le: {{ formatDateFr(shippingStateDate) }}</span>
          </div>
        </div>
      </div>
      <div
        class="my-2 flex w-full justify-center md:my-0 md:w-3/12 md:flex-col md:justify-start"
      >
        <span class="mr-2 flex font-bold text-primary md:mr-0"
          >{{ formatPrice(order.totalExcludingTaxes) }} € HT</span
        >
        <span class="flex text-gray-500"
          >({{ formatPrice(order.total) }} € TTC)</span
        >
      </div>
      <div class="flex items-center justify-between md:w-1/12 md:flex-col">
        <RouterLink
          :to="{
            name: PageList.ORDER_DETAILS,
            params: { id: order.id },
          }"
          class="rounded-lg border border-gray-500 p-2"
        >
          <EyeIconComponent class="h-[18px] w-[18px]" />
        </RouterLink>
        <button
          v-if="order.paymentId"
          class="rounded-lg p-2"
          title="Télécharger la facture"
          @click="downloadInvoice"
        >
          <DownloadIconComponent class="h-[20px] w-[20px] stroke-gray-500" />
        </button>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed, PropType } from 'vue'
import { Order } from '@/vuejs/types/Order'
import { formatDateFr, formatPrice } from '@/vuejs/services/utils'
import { ORDER_STATUS, SHIPPING_STATUS } from '@/vuejs/services/const'
import CalendarCheckIconComponent from '@/vuejs/modules/shared/icon/CalendarCheckIconComponent.vue'
import MapInIconComponent from '@/vuejs/modules/shared/icon/MapInIconComponent.vue'
import EyeIconComponent from '@/vuejs/modules/shared/icon/EyeIconComponent.vue'
import { PageList } from '@/vuejs/router'
import DownloadIconComponent from '@/vuejs/modules/shared/icon/DownloadIconComponent.vue'

const props = defineProps({
  order: {
    required: true,
    type: Object as PropType<Order>,
  },
})

const emit = defineEmits(['downloadInvoice'])

const shippingStateDate = computed(() => {
  switch (props.order.shippingState) {
    case 'preparation':
    case 'ready':
    case 'returned':
    case 'cancelled':
      return props.order.updatedAt
    case 'partially_shipped':
    case 'shipped':
      return props.order.shippedAt
    case 'delivered':
      return props.order.deliveredAt
    default:
      return props.order.createdAt
  }
})
const downloadInvoice = async () => {
  await emit('downloadInvoice', {
    paymentId: props.order.paymentId,
  })
}
</script>
