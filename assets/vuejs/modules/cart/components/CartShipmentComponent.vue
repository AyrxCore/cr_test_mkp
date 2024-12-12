<template>
  <h2 class="my-4 text-base text-primary md:text-xl lg:mt-0">
    Livraison {{ seller.name }}
    <slot name="order-index" />
  </h2>
  <div class="mb-5 rounded-lg bg-white p-5">
    <CartFrancoComponent :order="order" class="mb-4" />
    <p class="mb-4 font-bold text-primary">
      Sélectionnez votre méthode de livraison
    </p>
    <p class="mb-2 font-bold">Méthode(s) de livraison disponible(s) :</p>
    <div v-show="!isLoading">
      <div
        v-for="method in filteredShipmentMethods"
        class="flex items-center pb-2"
      >
        <input
          :id="`shipmentMethod-${method.shipping_method.id}`"
          v-model="selectedShippingMethod"
          :name="`shipmentMethod-${order.id}`"
          :value="method.shipping_method.id"
          class="checked:bg-secondary checked:hover:bg-secondary focus:bg-secondary focus:outline-none focus:ring-1 focus:ring-secondary checked:focus:bg-secondary checked:active:bg-secondary"
          type="radio"
          @change="selectShippingMethod"
        />
        <label
          :for="`shipmentMethod-${method.shipping_method.id}`"
          class="pl-2"
        >
          {{ method.shipping_method.name.fr }} -
          {{ hasReachedFranco ? 0 : method.amount / 100 }}€
        </label>
      </div>
      <template v-if="filteredShipmentMethods.length === 0">
        Aucune méthode de livraison disponible
      </template>
    </div>
    <LoaderSharedComponent v-show="isLoading" class="my-2" />
    <hr class="mt-2" />
    <div
      class="mt-4 flex cursor-pointer"
      @click="isDetailsOpen = !isDetailsOpen"
    >
      {{ order.items.length }} référence(s)
      <span class="ml-2 flex items-center font-bold text-secondary underline">
        <Chevron2RightIconComponent
          :class="{
            'rotate-90 ease-in-out': isDetailsOpen,
          }"
          class="mr-1 fill-secondary stroke-secondary text-sm lg:text-lg"
        />
        Détails
      </span>
    </div>
    <table v-if="isDetailsOpen" class="mt-4 w-full">
      <thead class="text-left font-bold">
        <tr>
          <th class="pb-2">Description de l'article</th>
          <th class="pb-2">Quantité</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(item, key) in order.items"
          :key="key"
          class="odd:bg-gray-50 even:bg-gray-100"
        >
          <td class="px-4 py-2">
            <span class="text-primary">{{
              item.variant.product.name.default
            }}</span
            ><br />
            <span class="text-sm">
              Référence : {{ item.variant.product.reference }}
            </span>
          </td>
          <td>{{ item.quantity }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script lang="ts" setup>
import { computed, onMounted, PropType, ref } from 'vue'
import { storeToRefs } from 'pinia'

import CartFrancoComponent from '@/vuejs/modules/cart/components/CartFrancoComponent.vue'
import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

import { useCartStore } from '@/vuejs/stores/cart'
import { SELLER_IDS, useSellerStore } from '@/vuejs/stores/seller'
import { Order, ShippingMethod } from '@/vuejs/types/Cart'
import { Seller } from '@/vuejs/types/Seller'

const cartStore = useCartStore()
const sellerStore = useSellerStore()
const { getHasReachedFranco } = storeToRefs(sellerStore)

const emit = defineEmits(['loaded'])

const props = defineProps({
  order: {
    required: true,
    type: Object as PropType<Order>,
  },
})

const isLoading = ref<boolean>(false)
const isDetailsOpen = ref<boolean>(false)
const shipmentMethods = cartStore.shippingMethods.filter((e) => {
  return e.order.id === props.order.id
})

const hasReachedFranco = computed((): boolean => {
  return getHasReachedFranco.value(props.order)
})

const filteredShipmentMethods = ref<ShippingMethod[]>(
  shipmentMethods.filter((e) => {
    if (props.order.seller.id === SELLER_IDS.KROMM) {
      // LIVRAISON VOLUMINEUX KRÖMM
      if (shipmentMethods.find((s) => s.shipping_method.id === 15)) {
        return e.shipping_method.id !== 14
      }
    }
    return true
  }),
)
const selectedShippingMethod = ref<number>(
  filteredShipmentMethods.value[0]?.shipping_method.id,
)

onMounted(async (): Promise<void> => {
  emit('loaded', false)
  isLoading.value = true
  const sellerId = props.order.seller.id
  await sellerStore.getSeller(sellerId)
  await selectShippingMethod()
  isLoading.value = false
  emit('loaded', true)
})

const seller = computed((): Seller => {
  return sellerStore.sellers.find((e) => e.id === props.order.seller.id)
})

const selectShippingMethod = async (): Promise<void> => {
  if (!selectedShippingMethod.value) return
  isLoading.value = true
  await cartStore.updateOrderShipping({
    cartId: cartStore.cart.id,
    orderId: props.order.id,
    shippingId: selectedShippingMethod.value,
  })
  await cartStore.getCart()
  isLoading.value = false
}
</script>
