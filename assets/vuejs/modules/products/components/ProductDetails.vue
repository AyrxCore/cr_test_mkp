<template>
  <div v-if="hasOptions" class="my-4">
    <div class="mb-2 text-lg font-bold text-primary md:text-xl">
      Mes options
    </div>
    <div
      v-for="(children, key, index) in product.options"
      :key="key"
      class="mt-2 flex w-full items-center justify-between bg-white px-4 py-2"
    >
      <span class="text-sm md:text-base lg:text-lg">
        {{ key }}
      </span>
      <select
        v-if="key && children.length > 0"
        v-model="option[index]"
        class="h-[1.75rem] w-1/2 border-none p-0"
        @change="updateProductVariant"
        @input="
          sendGaEvent('click_product_options', {
            product_name: product.name,
            partner_name: product.seller.name,
            partner_id: product.seller.id,
            option_id: option[index],
          })
        "
      >
        <option v-for="child in children" :key="child.id" :value="child.id">
          {{ child.value }}
        </option>
      </select>
    </div>
  </div>
  <!-- End Bloc options -->
  <!-- Fin product details -->
  <!-- Quantité + prix -->
  <div class="flex justify-between md:flex-col">
    <div class="lg:my-6">
      <div class="relative inline-flex items-center">
        <span class="mr-2 hidden md:block"> Quantité </span>
        <ProductQuantityComponent
          :quantity="product?.quantity"
          @update-quantity="updateQuantity"
          @update-quantity-input="updateQuantityInput"
        />
      </div>
    </div>
    <LoaderSharedComponent
      v-if="isLoadingPrice"
      class="text-secondary"
      classes="loader-lg loader"
    />
    <div v-else class="mb-4 flex items-end">
      <div
        v-if="product?.price"
        class="mr-2 text-xl font-bold text-primary md:text-3xl"
      >
        {{ product.price }}€ HT
      </div>
      <div
        v-if="product?.priceReference"
        :class="{
          'text-sm text-gray-500 line-through md:text-base lg:text-lg':
            product.price,
          'text-xl font-bold text-primary': product.price === null,
        }"
      >
        {{ product.priceReference }}€ HT
      </div>
    </div>
  </div>
  <!-- Fin Quantité + prix -->
  <!-- Bloc livraison -->
  <div v-if="product?.seller.description" class="mt-2">
    <h4 class="text-lg md:text-xl">Infos livraison</h4>
    <div class="mt-2 flex items-center">
      <TruckIconComponent class="mr-4 w-8 shrink-0 md:w-6" />
      {{ product.seller.description }}
    </div>
  </div>
  <!-- Fin Livraison -->
  <ProductAddToCartComponent :product="product" class="mt-4 hidden lg:flex" />
</template>

<script lang="ts" setup>
import { computed, onMounted, PropType, ref } from 'vue'

import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import ProductAddToCartComponent from '@/vuejs/modules/products/components/ProductAddToCartComponent.vue'
import ProductQuantityComponent from '@/vuejs/modules/shared/ProductQuantityComponent.vue'
import TruckIconComponent from '@/vuejs/modules/shared/icon/TruckIconComponent.vue'

import { Product } from '@/vuejs/types/Product'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { useProductStore } from '@/vuejs/stores/product'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const productStore = useProductStore()
const favoriteStore = useFavoriteStore()
const option = ref([])
const isLoadingPrice = ref<boolean>(false)

onMounted(async () => {
  await favoriteStore.fetchFavorites()
})

const hasOptions = computed((): boolean => {
  return Object.keys(props.product?.options)[0].length > 0
})

const updateQuantity = (event) => {
  const gtmEventName =
    props.product.quantity > event.quantity
      ? 'click_product_moins_qty'
      : 'click_product_plus_qty'
  sendGaEvent(gtmEventName, {
    product_name: props.product.name,
    qty_value: event.quantity,
  })
  props.product.quantity = event.quantity
}

const updateQuantityInput = (event) => {
  sendGaEvent('type_product_qty', {
    product_name: props.product.name,
    qty_value: event.quantity,
  })
  props.product.quantity = event.quantity
}

const updateProductVariant = async () => {
  isLoadingPrice.value = true
  props.product = await productStore.changeVariant(props.product, option.value)
  isLoadingPrice.value = false
}
</script>
