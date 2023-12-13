<template>
  <h2 class="mb-5 text-base text-primary md:text-xl">
    Livraison {{ seller.name }}
    <slot name="order-index" />
  </h2>
  <div class="mb-5 rounded-lg bg-white p-5">
    <CartFrancoComponent :order="order" class="mb-4" />
    <p class="mb-4 font-bold text-primary">
      Sélectionnez votre méthode de livraison
    </p>
    <p class="mb-2 font-bold text-gray-500">
      Méthode(s) de livraison disponible(s) :
    </p>
    <div v-show="!isLoading" class="text-gray-500">
      <div
        v-for="method in filteredShipmentMethods"
        class="flex items-center pb-2"
      >
        <input
          v-model="selectedShippingMethod"
          type="radio"
          :name="`shipmentMethod-${order.id}`"
          :id="`shipmentMethod-${method.shipping_method.id}`"
          :value="method.shipping_method.id"
          class="checked:bg-secondary checked:hover:bg-secondary focus:bg-secondary focus:outline-none focus:ring-1 focus:ring-secondary checked:focus:bg-secondary checked:active:bg-secondary"
          @change="selectShippingMethod"
        />
        <label
          :for="`shipmentMethod-${method.shipping_method.id}`"
          class="pl-2"
        >
          {{ method.shipping_method.name.fr }} - {{ method.amount / 100 }}€
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
          class="mr-1 fill-secondary stroke-secondary text-sm lg:text-lg"
          :class="{
            'rotate-90 ease-in-out': isDetailsOpen,
          }"
        />
        Détails
      </span>
    </div>
    <table v-if="isDetailsOpen" class="mt-4 w-full">
      <thead class="text-left font-bold text-gray-700">
        <tr>
          <th class="pb-2">Description de l'article</th>
          <th class="pb-2">Quantité</th>
        </tr>
      </thead>
      <tbody class="text-gray-500">
        <tr
          v-for="(item, key) in order.items"
          :class="`bg-gray-${key % 2 === 0 ? '50' : '100'}`"
        >
          <td class="px-4 py-2">
            {{ item.variant.product.name.default }}<br />
            <span class="text-sm text-gray-400">
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

import CartFrancoComponent from '@/vuejs/modules/cart/components/CartFrancoComponent.vue'
import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'

import { useCartStore } from '@/vuejs/stores/cart'
import { SELLER_IDS, useSellerStore } from '@/vuejs/stores/seller'
import { Order, ShippingMethod } from '@/vuejs/types/Cart'
import { Seller } from '@/vuejs/types/Seller'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

const cartStore = useCartStore()
const sellerStore = useSellerStore()

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

const filteredShipmentMethods = ref<ShippingMethod[]>(
  shipmentMethods.filter((e) => {
    if (props.order.seller.id === SELLER_IDS.KRÖMM) {
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
  isLoading.value = true
  const sellerId = props.order.seller.id
  await sellerStore.getSeller(sellerId)
  await selectShippingMethod()
  isLoading.value = false
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
