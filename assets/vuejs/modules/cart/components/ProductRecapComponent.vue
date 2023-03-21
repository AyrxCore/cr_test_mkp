<template>
  <div class="mx-auto h-full items-center bg-white px-5">
    <div class="flex w-full flex-col lg:flex-row">
      <div class="flex w-full flex-col lg:flex-row">
        <div class="my-5 flex lg:w-7/12">
          <!-- <div class="lg:ml-2">
            <CheckboxComponent />
          </div> -->
          <div class="flex h-[116px] w-5/12 lg:w-4/12">
            <img
              :src="productImage"
              :alt="`Image ${product.name.default}`"
              class="m-auto block max-h-full max-w-full"
            />
          </div>
          <div class="ml-4 w-6/12 lg:w-7/12">
            <h3 class="text-lg font-bold text-primary lg:text-[22px]">
              {{ product.name.default }}
            </h3>
            <!-- <span
            class="flex flex-col text-sm text-gray-500 md:flex-row lg:text-lg"
          >
            <span>Vendu par :</span>
            <span>{{ item.partner }}</span>
          </span> -->
            <span
              class="flex flex-col text-sm text-gray-500 lg:flex-row lg:text-lg"
            >
              Référence : {{ product.reference }}
            </span>
            <!-- <span class="flex text-sm text-green-400 lg:text-lg">
              En stock
            </span> -->
          </div>
        </div>
        <div
          class="mb-4 flex w-full items-center justify-between md:float-right lg:float-none lg:mb-0 lg:w-5/12"
        >
          <div class="text-center lg:w-2/12">
            <select
              v-if="!cartStore.modifyingCart"
              :value="item.quantity"
              class="w-20 rounded-lg border border-gray-300 text-center"
              @change="modifyQuantity"
            >
              <option>1</option>
              <option>2</option>
              <option>3</option>
              <option>4</option>
              <option>5</option>
              <option>6</option>
              <option>7</option>
              <option>8</option>
              <option>9</option>
              <option>10</option>
            </select>
            <LoaderSharedComponent v-else class="text-primary" />
          </div>
          <div class="text-center lg:w-4/12">
            <span class="mt-2 text-sm text-gray-400 line-through lg:text-lg">
              {{ referencePriceDisplayed }}€ HT
            </span>
          </div>
          <div class="text-center lg:w-4/12">
            <span class="mt-2 text-sm font-bold text-primary lg:text-lg">
              {{ totalPriceDisplayed }}€ HT
            </span>
          </div>
          <div class="flex">
            <!-- <button class="flex text-gray-500">
              <HeartIconComponent class="mr-2 stroke-gray-500" />
            </button> -->
            <button
              v-if="!cartStore.modifyingCart"
              class="flex text-gray-500"
              @click="deleteProduct"
            >
              <TrashIconComponent :stroke-color="'#5E6875'" />
            </button>
            <LoaderSharedComponent v-else class="text-primary" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { ref, computed, PropType, onMounted } from 'vue'

import { getImage, formatPrice } from '@/vuejs/services/utils'

import { OrderItem, OrderProduct } from '@/vuejs/types/Cart'
import { useCartStore } from '@/vuejs/stores/cart'
import { useProductStore } from '@/vuejs/stores/product'
import { Product } from '@/vuejs/types/Product'

import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import CheckboxComponent from '@/vuejs/modules/shared/CheckboxComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import sampleImg from '@/vuejs/assets/img/sample_product_img.png'

const cartStore = useCartStore()
const productStore = useProductStore()

const props = defineProps({
  item: {
    required: true,
    type: Object as PropType<OrderItem>,
  },
})

const product = ref<OrderProduct>(props.item.variant.product)
const productData = ref<Product>()

onMounted(async (): Promise<void> => {
  productData.value = await productStore.findProductById(product.value.id)
})

const productImage = computed((): string => {
  if (productData.value) return productData.value.images[0]
  return getImage(sampleImg)
})

const referencePrice = computed((): number => {
  return product.value.price_reference / 100
})

const totalPrice = computed((): number => {
  return props.item.total_excluding_taxes / 100
})

const totalPriceDisplayed = computed((): string => {
  return formatPrice(totalPrice.value)
})

const referencePriceDisplayed = computed((): string => {
  const price =
    referencePrice.value > 0
      ? referencePrice.value * props.item.quantity
      : totalPrice.value
  return formatPrice(price)
})

const modifyQuantity = async (event: InputEvent): Promise<void> => {
  if (cartStore.modifyingCart) return
  cartStore.modifyingCart = true
  await cartStore.updateProductQuantity({
    id: props.item.id,
    quantity: parseInt(event.target.value),
  })
  await cartStore.getCart()
  cartStore.modifyingCart = false
}

const deleteProduct = async (): Promise<void> => {
  if (cartStore.modifyingCart) return
  cartStore.modifyingCart = true
  await cartStore.deleteProduct(props.item.id)
  await cartStore.getCart()
  cartStore.modifyingCart = false
}
</script>
