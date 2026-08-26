<template>
  <div class="mb-5 flex flex-col rounded-lg bg-white text-sm lg:text-[15px]">
    <div
      class="flex w-full flex-col px-2.5 py-2.5 md:flex-row md:items-start md:py-5"
    >
      <div class="flex w-full justify-start md:w-2/12 lg:w-3/12">
        <span class="flex pl-5"> {{ formatDateFr(order.createdAt) }}</span>
      </div>
      <div class="mt-5 w-full md:mt-0 md:w-5/12 lg:w-4/12">
        <span class="flex"> Commande : {{ order.orderNumber }}</span>
        <span class="flex"> Article(s) : {{ order.productCount }} </span>


      </div>
      <div class="mt-5 w-full md:mt-0 md:w-5/12">
        <span class="md:hidden">Livraison</span>
        <span class="flex md:pr-4">
          {{ order.shippingAddress }}
        </span>
      </div>
      <div
        class="my-2 flex w-full justify-center md:my-0 md:w-3/12 md:flex-col md:justify-start"
      >
        <span class="mr-2 flex font-bold text-primary md:mr-0"
          >{{ formatPrice(order.totalExcludingTaxes) }} € HT</span
        >
        <span class="flex">({{ formatPrice(order.total) }} € TTC)</span>
      </div>
      <div class="flex items-center justify-center md:w-1/12">
        <RouterLink
          :to="{
            name: PageList.ORDER_DETAILS,
            params: { id: order.id },
          }"
          class="rounded-lg border border-primary p-1.5"
          @click="
            sendGtmEvent('order_view', {
              order_id: order.id,
              order_price: formatPrice(order.total),
            })
          "
        >
          <EyeIconComponent
            :stroke="channelPrimaryColor"
            class="h-[22px] w-[22px]"
          />
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { PropType } from 'vue'
import { storeToRefs } from 'pinia'

import { PageList } from '@/vuejs/router'
import { useChannelStore } from '@/vuejs/stores/channel'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { formatDateFr, formatPrice } from '@/vuejs/services/utils'
import { Order } from '@/vuejs/types/Order'

import EyeIconComponent from '@/vuejs/modules/shared/icon/EyeIconComponent.vue'

defineProps({
  order: {
    required: true,
    type: Object as PropType<Order>,
  },
})

const { channelPrimaryColor } = storeToRefs(useChannelStore())

</script>
