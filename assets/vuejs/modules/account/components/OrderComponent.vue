<template>
  <div class="mb-5 flex flex-col rounded-lg bg-white text-sm lg:text-[15px]">
    <div class="flex w-full flex-col px-5 py-2.5 md:flex-row md:py-5">
      <div class="flex w-full justify-start md:w-2/12 lg:w-3/12">
        <span class="flex"> {{ formatDateFr(order.createdAt) }}</span>
      </div>
      <div class="mt-5 w-full md:mt-0 md:w-5/12 lg:w-4/12">
        <span class="flex"> Commande : {{ order.orderNumber }}</span>
        <span class="flex"> Articles : {{ order.items.length }} </span>

        <div class="flex items-center space-x-2 md:my-2">
          <span>Statut :</span>
          <span
            :class="ORDER_STATUS[order.state].color"
            :title="ORDER_STATUS[order.state].name"
            class="ml-2 mt-1 w-fit rounded-md px-1 py-1 text-[14px] text-white"
            >{{ ORDER_STATUS[order.state].name }}</span
          >
        </div>
      </div>
      <div class="mt-5 w-full md:mt-0 md:w-5/12">
        <span class="md:hidden">Livraison</span>
        <div class="flex flex-col">
          <span class="flex md:pr-4">
            {{ order.shippingAddress }}
          </span>
          <div class="my-2 flex flex-col">
            <div class="flex items-center space-x-2">
              <span>Statut :</span>
              <span
                :class="SHIPPING_STATUS[order.shippingState].color"
                :title="SHIPPING_STATUS[order.shippingState].name"
                class="ml-2 w-fit rounded-md px-1 py-1 text-[14px] text-white"
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
        <span class="flex">({{ formatPrice(order.total) }} € TTC)</span>
      </div>
      <div class="flex items-start justify-between md:w-1/12">
        <ButtonDownloadInvoiceComponent
          v-if="order.paymentId"
          :payment-id="order.paymentId"
          @click="
            sendGtmEvent('order_download', {
              order_id: order.id,
              order_price: formatPrice(order.total),
            })
          "
        />
        <RouterLink
          :to="{
            name: PageList.ORDER_DETAILS,
            params: { id: order.id },
          }"
          class="rounded-lg border border-primary p-0.5"
          @click="
            sendGtmEvent('order_view', {
              order_id: order.id,
              order_price: formatPrice(order.total),
            })
          "
        >
          <EyeIconComponent
            :stroke="channelPrimaryColor"
            class="h-[18px] w-[18px]"
          />
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'
import { storeToRefs } from 'pinia'

import { PageList } from '@/vuejs/router'
import { useChannelStore } from '@/vuejs/stores/channel'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { formatDateFr, formatPrice } from '@/vuejs/services/utils'
import { ORDER_STATUS, SHIPPING_STATUS } from '@/vuejs/services/const'
import { Order } from '@/vuejs/types/Order'

import ButtonDownloadInvoiceComponent from '@/vuejs/modules/account/components/ButtonDownloadInvoiceComponent.vue'
import EyeIconComponent from '@/vuejs/modules/shared/icon/EyeIconComponent.vue'

const props = defineProps({
  order: {
    required: true,
    type: Object as PropType<Order>,
  },
})

const { channelPrimaryColor } = storeToRefs(useChannelStore())

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
</script>
