<template>
  <div class="mx-auto h-full items-center bg-white px-5">
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
              :alt="`Image ${product.name}`"
              :src="productImage"
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
              {{ product.name }}
            </RouterLink>

            <span
              v-if="productNotFound"
              class="text-white"
              title="Ce produit n'est plus disponible"
            >
              <WarningIconComponent
                class="ml-2 h-[24px] fill-orange-500 text-primary"
              />
            </span>
          </h3>
          <span class="flex flex-col text-sm lg:flex-row lg:text-lg">
            Référence : {{ product.externalId }}
          </span>
          <span
            v-if="options && Object.keys(options).length > 0"
            class="flex cursor-pointer items-center text-sm lg:text-lg"
            @click="isOpen = !isOpen"
          >
            <Chevron2RightIconComponent
              :class="{
                'rotate-90 ease-in-out': isOpen,
              }"
              class="mr-1 text-sm lg:text-lg"
            />
            Détails
          </span>
          <div
            v-if="isLoadingOptions"
            class="mt-1 flex h-10 w-full items-center justify-start"
          >
            <LoaderSharedComponent class="loader-lg loader" />
          </div>
          <div v-else-if="isOpen">
            <ul>
              <li
                v-for="(option, key) in options"
                :key="key"
                class="ml-3 text-sm lg:text-base"
              >
                <span class="font-bold"> {{ key }} : </span>
                <span class="italic">{{ option.values[0] }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div
        class="mb-4 flex w-full items-center justify-between lg:mb-0 lg:w-5/12"
      >
        <div class="flex items-center justify-center lg:w-2/12">
          <ProductQuantityComponent
            v-if="!cartStore.modifyingCart"
            :quantity="product.quantity"
            @update-quantity="modifyQuantity"
          />
          <LoaderSharedComponent v-else class="text-primary" />
        </div>
        <div class="flex items-center justify-center lg:w-4/12">
          <SkeletonLoading v-if="isLoadingPrices" class="flex justify-center" />
          <span v-else class="text-sm line-through lg:text-lg">
            {{ referencePriceDisplayed }}€ HT
          </span>
        </div>
        <div class="flex items-center justify-center lg:w-4/12">
          <SkeletonLoading v-if="isLoadingPrices" class="flex justify-center" />
          <div v-else class="flex flex-col items-center">
            <span class="text-sm font-bold text-primary lg:text-lg">
              {{ totalPriceDisplayed }}€ HT
            </span>
            <span
              v-if="ecoTaxTotal"
              class="mt-0.5 rounded px-1.5 py-0.5 text-xs text-gray-700"
            >
              dont {{ ecoTaxTotal }}€ d'éco-part
            </span>
          </div>
        </div>
        <div class="flex items-center justify-center lg:w-1/12">
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
</template>
<script lang="ts" setup>
import { computed, onMounted, PropType, ref } from 'vue'

import { formatPrice, notifyError, notifySuccess } from '@/vuejs/services/utils'

import { useCartStore } from '@/vuejs/stores/cart'
import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import WarningIconComponent from '@/vuejs/modules/shared/icon/WarningIconComponent.vue'
import { PageList } from '@/vuejs/router'
import ProductQuantityComponent from '../../shared/ProductQuantityComponent.vue'
import { Product } from '@/vuejs/types/Product.ts'
import { Variant } from '@/vuejs/types/Variant.ts'
import SkeletonLoading from '@/vuejs/modules/shared/SkeletonLoading.vue'

const cartStore = useCartStore()
const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const emit = defineEmits(['is-loading-prices'])

const isOpen = ref(false)
const productNotFound = ref(false)
const isLoadingOptions = ref<boolean>(false)
const isLoadingPrices = ref<boolean>(false)

onMounted(async (): Promise<void> => {
  if (!props.product) {
    productNotFound.value = true
  }
})

const variant = computed((): Variant => {
  return props.product ? props.product.variants[0] : null
})

const productImage = computed((): string => {
  return props.product ? props.product.images[0] : null
})

const options = computed((): Record<string, any> => {
  return props.product.options ?? null
})

const referencePrice = computed((): number => {
  return props.product.priceReference * props.product.quantity
})

const totalPrice = computed((): number => {
  return props.product.price * props.product.quantity
})

const productId = computed(() => {
  return props.product
    ? { slug: props.product.slug }
    : { slug: variant.value.offerPriceExternalId }
})

const totalPriceDisplayed = computed((): string => {
  return formatPrice(totalPrice.value)
})

const ecoTaxTotal = computed((): string | null => {
  const unitEcoTax = props.product?.ecoTax
  if (!unitEcoTax) return null
  const total = unitEcoTax * (props.product.quantity ?? 1)
  return formatPrice(total)
})

const referencePriceDisplayed = computed((): string => {
  const price =
    referencePrice.value > 0 ? referencePrice.value : totalPrice.value
  return formatPrice(price)
})

const modifyQuantity = async (event): Promise<void> => {
  if (cartStore.modifyingCart) return
  cartStore.modifyingCart = true
  isLoadingPrices.value = true
  emit('is-loading-prices', true)
  try {
    const removedIds = await cartStore.syncCart()
    const currentOfferPriceId = props.product.variants[0].offerPriceExternalId

    if (removedIds.includes(currentOfferPriceId)) {
      await cartStore.getCart()
      notifyError('Ce produit a été retiré du catalogue et a été supprimé de votre panier.')
      return
    }

    props.product.quantity = event.quantity
    await cartStore.updateProductsToCart([
      {
        offerPriceId: currentOfferPriceId,
        quantity: parseInt(event.quantity),
      },
    ])
    await cartStore.getCart()
    if (cartStore.needsProductFdpSync) {
      await cartStore.syncProductsFdp()
    }
  } finally {
    isLoadingPrices.value = false
    emit('is-loading-prices', false)
    cartStore.modifyingCart = false
  }
}

const deleteProduct = async (): Promise<void> => {
  if (cartStore.modifyingCart) return
  try {
    cartStore.modifyingCart = true
    await cartStore.removeProductsToCart([
      { offerPriceId: variant.value.offerPriceExternalId },
    ])
    await cartStore.getCart()
    if (cartStore.needsProductFdpSync) {
      await cartStore.syncProductsFdp()
    }
    notifySuccess('La référence du produit a été retirée au panier')
  } finally {
    cartStore.modifyingCart = false
  }
}
</script>
