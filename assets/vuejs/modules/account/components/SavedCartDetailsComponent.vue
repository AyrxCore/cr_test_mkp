<template>
  <div
    class="mb-2.5 flex flex-col rounded-lg bg-white p-2.5 text-lg md:flex-row"
  >
    <div class="flex flex-col items-center md:w-6/12 md:flex-row">
      <div class="flex md:w-5/12">
        <img
          v-if="productImage"
          :src="productImage"
          alt="Image produit"
          class="flex h-full w-full max-w-max cursor-pointer items-center"
        />
        <div
          v-else
          class="loading flex h-[116px] w-full items-center justify-center rounded-lg px-6 py-2"
        />
      </div>
      <div class="mt-4 flex flex-col pr-4 md:my-2 md:ml-5 md:w-7/12">
        <RouterLink
          :to="{ name: PageList.PRODUCT, params: { slug: productSlug } }"
          class="font-cotext text-lg font-bold text-primary lg:text-2xl"
        >
          {{ productName }}
        </RouterLink>
        <span class="flex flex-col text-sm lg:text-lg">
          <span>Vendu par : {{ productSeller }}</span>
          <span>Référence : {{ productReference }}</span>
        </span>
      </div>
    </div>
    <div class="mt-4 flex w-full justify-end md:w-2/12 md:justify-start">
      <span class="text-sm font-bold text-primary md:text-base lg:text-lg">
        <span class="mr-2 text-base md:hidden">Prix unitaire : </span
        >{{ productUnitPrice }}€ HT
      </span>
    </div>
    <div class="flex w-full justify-between md:w-4/12">
      <div class="mt-4 flex justify-end md:w-6/12 md:justify-start">
        <ProductQuantityComponent
          :quantity="quantity"
          @update-quantity="changeQuantity"
        />
      </div>
      <div class="mt-4 flex justify-end md:w-6/12 md:justify-start">
        <span class="mr-4 text-lg font-bold text-primary md:mr-0">
          {{ productPrice }}€ HT
        </span>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, onMounted, PropType, ref } from 'vue'

import { PageList } from '@/vuejs/router'
import { useProductStore } from '@/vuejs/stores/product'
import { formatPrice } from '@/vuejs/services/utils'
import { Product } from '@/vuejs/types/Product'
import { SavedCartProduct } from '@/vuejs/types/SavedCart'

import ProductQuantityComponent from '../../shared/ProductQuantityComponent.vue'

const emit = defineEmits(['changeQuantity'])
const props = defineProps({
  savedCartProduct: {
    required: true,
    type: Object as PropType<SavedCartProduct>,
  },
})

const productStore = useProductStore()
const product = ref<Product>()
const variantData = ref()
const productNotFound = ref(false)
const quantity = ref<number>(parseInt(props.savedCartProduct.quantity))

onMounted(async (): Promise<void> => {
  product.value = await productStore.initProduct(
    props.savedCartProduct.upplerProductId,
  )
  if (!product.value) {
    productNotFound.value = true
  } else {
    if (Object.entries(product.value?.variants).length > 1) {
      variantData.value = await productStore.findVariantById(
        props.savedCartProduct.upplerVariantId,
      )
    }
    await emit('changeQuantity', {
      variantId: props.savedCartProduct.upplerVariantId,
      quantity: quantity.value,
      price: product.value.price * quantity.value,
    })
  }
})

const changeQuantity = async (event) => {
  quantity.value = event.quantity
  await emit('changeQuantity', {
    variantId: props.savedCartProduct.upplerVariantId,
    quantity: quantity.value,
    price: product.value.price * quantity.value,
  })
}

const productImage = computed((): string => {
  return product.value ? product.value.images[0] : null
})

const productSlug = computed(() => {
  return product.value
    ? product.value.slug
    : props.savedCartProduct.upplerProductId
})

const productName = computed((): string => {
  return product.value
    ? product.value.name
    : props.savedCartProduct.upplerProductName
})

const productReference = computed((): string => {
  return product.value ? product.value.reference : ''
})

const productPrice = computed((): number | string => {
  let price = 0

  if (variantData.value) {
    price = (variantData.value.price?.display_price / 100) * quantity.value
  } else if (product.value) {
    price = product.value.price * quantity.value
  }

  return formatPrice(price)
})

const productUnitPrice = computed((): number | string => {
  return product.value ? product.value.price : ''
})

const productSeller = computed((): string => {
  return product.value ? product.value.seller.name : ''
})
</script>
<style scoped>
.input-qte {
  @apply rounded-lg border border-gray-300 px-0 text-center text-sm md:text-base lg:text-lg;
}
</style>
