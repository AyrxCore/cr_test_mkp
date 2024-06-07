<template>
  <div class="mx-auto h-full items-center bg-white px-5">
    <div class="flex w-full flex-col lg:flex-row">
      <div class="flex w-full flex-col lg:flex-row">
        <div class="my-5 flex lg:w-7/12">
          <div class="flex h-[116px] w-5/12 lg:w-4/12">
            <RouterLink
              :to="{
                name: PageList.PRODUCT,
                params: productId,
              }"
              class="contents"
            >
              <img
                v-if="productImage"
                :src="productImage"
                :alt="`Image ${product.name.default}`"
                class="m-auto block max-h-full max-w-full"
              />
              <div
                v-else
                class="loading flex h-[116px] w-full items-center justify-center rounded-lg px-6 py-2"
              ></div>
            </RouterLink>
          </div>
          <div class="ml-4 w-6/12 lg:w-7/12">
            <h3
              class="flex items-center text-lg font-bold text-primary lg:text-[22px]"
            >
              <RouterLink
                :to="{
                  name: PageList.PRODUCT,
                  params: productId,
                }"
              >
                {{ product.name.default }}
              </RouterLink>

              <span
                v-if="product && productNotFound"
                class="text-white"
                title="Ce produit n'est plus disponible"
              >
                <WarningIconComponent
                  class="ml-2 h-[24px] fill-orange-500 text-primary"
                />
              </span>
            </h3>
            <span class="flex flex-col text-sm lg:flex-row lg:text-lg">
              Référence : {{ product.reference }}
            </span>
            <span
              class="flex cursor-pointer items-center text-sm lg:text-lg"
              @click="variantOptions"
            >
              <Chevron2RightIconComponent
                class="mr-1 text-sm lg:text-lg"
                :class="{
                  'rotate-90 ease-in-out': isOpen,
                }"
              />
              Détails
            </span>
            <div
              v-if="isLoadingOptions"
              class="mt-1 flex h-10 w-full items-center justify-start"
            >
              <LoaderSharedComponent class="loader-lg loader" />
            </div>
            <div v-else>
              <ul v-if="isOpen && options.length > 0">
                <li
                  v-for="option in options"
                  :key="option.id"
                  class="ml-3 text-sm lg:text-base"
                >
                  <span v-if="option.option.name.default" class="font-bold">
                    {{ option.option.name.default }} :
                  </span>
                  <span class="italic">{{ option.value.default }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div
          class="mb-4 flex w-full items-center justify-between md:float-right lg:float-none lg:mb-0 lg:w-5/12"
        >
          <div class="text-center lg:w-2/12">
            <ProductQuantityComponent
              v-if="!cartStore.modifyingCart"
              :quantity="item.quantity"
              @update-quantity="modifyQuantity"
            />

            <LoaderSharedComponent v-else class="text-primary" />
          </div>
          <div class="text-center lg:w-4/12">
            <span class="mt-2 text-sm line-through lg:text-lg">
              {{ referencePriceDisplayed }}€ HT
            </span>
          </div>
          <div class="text-center lg:w-4/12">
            <span class="mt-2 text-sm font-bold text-primary lg:text-lg">
              {{ totalPriceDisplayed }}€ HT
            </span>
          </div>
          <div class="flex">
            <button
              v-if="!cartStore.modifyingCart"
              class="flex text-gray-500"
              @click="deleteProduct"
            >
              <TrashIconComponent stroke="#5E6875" />
            </button>
            <LoaderSharedComponent v-else class="text-primary" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed, onMounted, PropType, ref } from 'vue'

import { formatPrice, notifySuccess } from '@/vuejs/services/utils'

import { OrderItem, OrderProduct } from '@/vuejs/types/Cart'
import { useCartStore } from '@/vuejs/stores/cart'
import { useProductStore } from '@/vuejs/stores/product'
import { Product } from '@/vuejs/types/Product'
import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import WarningIconComponent from '@/vuejs/modules/shared/icon/WarningIconComponent.vue'
import { PageList } from '@/vuejs/router'
import ProductQuantityComponent from '../../shared/ProductQuantityComponent.vue'

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
const options = ref([])
const isOpen = ref(false)
const productNotFound = ref(false)
const isLoadingOptions = ref<boolean>(false)

onMounted(async (): Promise<void> => {
  productData.value = await productStore.initProduct(product.value.id)
  if (!productData.value) {
    productNotFound.value = true
  }
})

const productImage = computed((): string => {
  return productData.value ? productData.value.images[0] : null
})

const variantOptions = async () => {
  isOpen.value = !isOpen.value
  if (options.value.length === 0) {
    isLoadingOptions.value = true
    const variant = await productStore.findVariantById(props.item.variant.id)
    options.value = variant.option_values
    isLoadingOptions.value = false
  }
}

const referencePrice = computed((): number => {
  return product.value.price_reference / 100
})

const totalPrice = computed((): number => {
  return props.item.total_excluding_taxes / 100
})

const productId = computed(() => {
  return productData.value
    ? { slug: productData.value.slug }
    : { slug: product.value.id }
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

const modifyQuantity = async (event): Promise<void> => {
  if (cartStore.modifyingCart) return
  cartStore.modifyingCart = true
  try {
    props.item.quantity = event.quantity
    await cartStore.updateProductQuantity({
      id: props.item.id,
      quantity: parseInt(event.quantity),
    })
    await cartStore.getCart()
  } catch (e) {
  } finally {
    cartStore.modifyingCart = false
  }
}

const deleteProduct = async (): Promise<void> => {
  if (cartStore.modifyingCart) return
  try {
    cartStore.modifyingCart = true
    await cartStore.deleteProduct(props.item.id)
    await cartStore.getCart()
    notifySuccess('La référence du produit a été retirée au panier')
  } catch (e) {
  } finally {
    cartStore.modifyingCart = false
  }
}
</script>
