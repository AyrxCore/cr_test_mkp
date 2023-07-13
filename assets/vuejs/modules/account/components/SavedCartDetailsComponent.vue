<template>
  <div
    class="mb-2.5 flex flex-col rounded-lg bg-white p-2.5 text-lg text-gray-500 md:flex-row"
  >
    <div class="flex md:w-8/12 lg:w-9/12">
      <div class="flex w-6/12 md:w-3/12">
        <img
          v-if="productImage"
          :src="productImage"
          alt="Image produit"
          class="flex h-full w-full max-w-max cursor-pointer items-center"
        />
        <div
          v-else
          class="loading flex h-[116px] w-full items-center justify-center rounded-lg px-6 py-2"
        ></div>
      </div>
      <div class="flex w-6/12 flex-col md:ml-5 md:w-7/12">
        <RouterLink
          :to="{ name: PageList.PRODUCT, params: productId }"
          class="text-lg font-bold text-primary lg:text-[22px]"
        >
          {{ productName }}
        </RouterLink>
        <span class="flex flex-col text-sm text-gray-500 lg:text-lg">
          <span>Vendu par: {{ productSeller }}</span>
          <span>Référence: {{ productReference }}</span>
        </span>
        <span class="mt-2 flex text-sm text-green-400 lg:text-lg"
          >En stock</span
        >
      </div>
    </div>
    <div
      class="flex w-full items-center justify-between px-2 md:w-4/12 md:px-0 md:pr-2"
    >
      <div class="flex">
        <ProductQuantityComponent
          :quantity="quantity"
          @update-quantity="changeQuantity"
        />
      </div>
      <div class="flex">
        <div class="md:justify-end">
          <div
            class="flex w-full flex-row flex-wrap items-center justify-between md:w-auto"
          >
            <span
              class="flex items-center text-sm font-bold text-primary md:text-base lg:text-lg"
            >
              {{ productPrice }}€ HT
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed, onMounted, PropType, ref } from 'vue'
import { formatPrice } from '@/vuejs/services/utils'
import { Product } from '@/vuejs/types/Product'
import { useProductStore } from '@/vuejs/stores/product'
import { PageList } from '@/vuejs/router'
import ProductQuantityComponent from '../../shared/ProductQuantityComponent.vue'
import { SavedCartProduct } from '@/vuejs/types/SavedCart'

const productStore = useProductStore()
const product = ref<Product>()
const variantData = ref()
const productNotFound = ref(false)
const quantity = ref<number>()
const emit = defineEmits(['changeQuantity'])
const props = defineProps({
  savedCartProduct: {
    required: true,
    type: Object as PropType<SavedCartProduct>,
  },
})

onMounted(async (): Promise<void> => {
  product.value = await productStore.findProductById(
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
  }
  quantity.value = parseInt(props.savedCartProduct.quantity)
})

const changeQuantity = async (event) => {
  quantity.value = event.quantity

  await emit('changeQuantity', {
    variantId: props.savedCartProduct.upplerVariantId,
    quantity: quantity.value,
  })
}

const productImage = computed((): string => {
  return product.value ? product.value.images[0] : null
})

const productId = computed(() => {
  return product.value
    ? { id: product.value.slug + '-' + product.value.id }
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

const productSeller = computed((): string => {
  return product.value ? product.value.seller.name : ''
})
</script>
<style scoped>
.input-qte {
  @apply rounded-lg border border-gray-300 px-0 text-center text-sm md:text-base lg:text-lg;
}
</style>
