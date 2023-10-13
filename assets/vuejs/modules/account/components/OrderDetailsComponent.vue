<template>
  <div
    class="mb-2.5 flex flex-col rounded-lg bg-white p-2.5 text-lg text-gray-500 md:flex-row"
  >
    <div class="flex md:w-8/12 lg:w-9/12">
      <div class="flex h-fit w-6/12 md:w-3/12">
        <img
          :src="productImage"
          :alt="productName"
          class="h-full w-full object-contain"
        />
      </div>
      <div class="flex w-6/12 flex-col pr-3 md:ml-3 md:w-8/12">
        <RouterLink
          :to="{ name: PageList.PRODUCT, params: { slug: productSlug } }"
          class="text-lg font-bold text-primary lg:text-lg"
        >
          {{ productName }}
        </RouterLink>
        <span class="flex flex-col text-sm text-gray-500 lg:text-lg">
          <span>Vendu par: {{ productSeller }}</span>
          <span>Référence: {{ productReference }}</span>
          <span>Quantité: {{ item.quantity }}</span>
        </span>
        <span class="mt-2 flex text-sm text-green-400 lg:text-lg"
          >En stock</span
        >
      </div>
    </div>
    <div class="md:w-4/12 lg:w-3/12">
      <div class="flex md:justify-between">
        <div
          class="flex w-full flex-row flex-wrap items-center items-center justify-between md:w-auto"
        >
          <span
            class="flex items-start items-center text-sm font-bold text-primary md:text-base lg:text-lg"
          >
            {{ productPrice }}€ HT
          </span>
        </div>
        <div
          class="flex w-full flex-row flex-wrap items-center items-center justify-between md:w-auto"
        >
          <span
            class="flex items-start items-center text-sm font-bold md:text-base lg:text-lg"
          >
            {{ productTotalPrice }}€ HT
          </span>
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
const product = ref<Product>()
const productNotFound = ref(false)
const priceReference = ref()
const price = ref()
const percent = ref()
const props = defineProps({
  item: {
    required: true,
    type: Object,
  },
})

onMounted(async (): Promise<void> => {
  product.value = await productStore.initProduct(props.item.variant.product.id)
  if (!product.value) {
    productNotFound.value = true
  } else {
    priceReference.value = product.value.priceReference
    price.value = product.value.price
    percent.value = product.value.percent
  }
})

const productImage = computed((): string => {
  if (product.value) return product.value.images[0]
  return getImage(sampleImg)
})

const productSlug = computed((): string => {
  return product.value ? product.value.slug : props.item.variant.product.id
})

const productName = computed((): string => {
  return product.value
    ? product.value.name
    : props.item.variant.product.name.defauult
})

const productReference = computed((): string => {
  return product.value ? product.value.reference : ''
})

const productPrice = computed((): number | string => {
  return product.value ? formatPrice(product.value.price) : ''
})

const productTotalPrice = computed((): number | string => {
  return formatPrice(props.item.total_excluding_taxes / 100)
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
