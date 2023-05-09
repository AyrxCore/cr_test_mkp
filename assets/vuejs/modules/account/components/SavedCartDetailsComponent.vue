<template>
  <div
    class="mb-2.5 flex flex-col rounded-lg bg-white p-2.5 text-lg text-gray-500 md:flex-row"
  >
    <div class="flex md:w-8/12 lg:w-9/12">
      <div class="flex w-6/12 md:w-3/12">
        <img
          :src="productImage"
          alt="Image produit"
          class="flex h-full w-full max-w-max cursor-pointer items-center"
        />
      </div>
      <div class="flex w-6/12 flex-col md:ml-5 md:w-7/12">
        <RouterLink
          :to="{ name: PageList.PRODUCT, params: { id: productId } }"
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
        <select
          v-model.number="product.quantity"
          class="w-20 rounded-lg border border-gray-300 text-center"
          @change="changeQuantity"
        >
          <option v-for="i in 10" :key="i">{{ i }}</option>
        </select>
      </div>
      <div class="flex">
        <div class="md:justify-end">
          <div
            class="flex w-full flex-row flex-wrap items-center items-center justify-between md:w-auto"
          >
            <span
              class="flex items-start items-center text-sm font-bold text-primary md:text-base lg:text-lg"
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
import { computed, onMounted, ref } from 'vue'
import { formatPrice, getImage } from '@/vuejs/services/utils'
import sampleImg from '@/vuejs/assets/img/sample_product_img.png'
import { Product } from '@/vuejs/types/Product'
import { useProductStore } from '@/vuejs/stores/product'
import { PageList } from '@/vuejs/router'

const productStore = useProductStore()
const productData = ref<Product>()
const variantData = ref()
const productNotFound = ref(false)
const price = ref('')

const emit = defineEmits(['changeQuantity'])
const props = defineProps({
  product: {
    required: true,
    type: Object,
  },
})

onMounted(async (): Promise<void> => {
  productData.value = await productStore.findProductById(
    props.product.upplerProductId,
  )

  if (!productData.value) {
    productNotFound.value = true
  } else {
    if (Object.entries(productData.value?.variants).length > 1) {
      variantData.value = await productStore.findVariantById(
        props.product.upplerVariantId,
      )
    }
  }
})

const changeQuantity = async () => {
  await emit('changeQuantity', {
    variantId: props.product.upplerVariantId,
    quantity: props.product.quantity,
  })
}

const productImage = computed((): string => {
  if (productData.value) return productData.value.images[0]
  return getImage(sampleImg)
})

const productId = computed((): string => {
  return props.product.upplerProductId
})

const productName = computed((): string => {
  return productData.value
    ? productData.value.name
    : props.product.upplerProductName
})

const productReference = computed((): string => {
  return productData.value ? productData.value.reference : ''
})

const productPrice = computed((): number | string => {
  if (variantData.value) {
    price.value =
      (variantData.value.price?.display_price / 100) * props.product.quantity
  } else if (productData.value) {
    price.value = productData.value.price?.displayPrice * props.product.quantity
  }
  return formatPrice(price.value)
})

const productSeller = computed((): string => {
  return productData.value ? productData.value.seller.name : ''
})
</script>
<style scoped>
.input-qte {
  @apply rounded-lg border border-gray-300 px-0 text-center text-sm md:text-base lg:text-lg;
}
</style>
