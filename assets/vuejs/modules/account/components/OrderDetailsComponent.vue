<template>
  <div
    class="mb-2.5 flex flex-col rounded-lg bg-white p-2.5 text-lg md:flex-row"
  >
    <div class="flex md:w-8/12 lg:w-9/12">
      <div class="flex h-fit w-6/12 md:w-3/12">
        <img
          :alt="productName"
          :src="productImage"
          class="h-full w-full object-contain"
        />
      </div>
      <div class="flex w-6/12 flex-col pr-3 md:ml-10 md:w-8/12">
        <RouterLink
          :to="{ name: PageList.PRODUCT, params: { slug: productSlug } }"
          class="text-lg font-bold text-primary lg:text-lg"
        >
          {{ productName }}
        </RouterLink>
        <span class="flex flex-col text-sm lg:text-lg">
          <span>Vendu par : {{ productSeller }}</span>
          <span>Référence : {{ productReference }}</span>
          <span>Quantité : {{ item.quantity }}</span>
        </span>
      </div>
    </div>
    <div class="mt-4 md:mt-0 md:w-4/12 lg:w-3/12">
      <div class="flex md:justify-between">
        <div
          class="flex w-full flex-col flex-wrap items-center justify-between md:w-auto"
        >
          <span
            class="flex items-start text-sm font-bold text-primary md:text-base lg:text-lg"
          >
            <span class="mr-3 md:hidden">Prix unitaire :</span>
            {{ productPrice }}€ HT
          </span>
        </div>
        <div
          class="flex w-full flex-col flex-wrap items-end justify-between md:w-auto"
        >
          <span
            class="flex items-start text-sm font-bold md:text-base lg:text-lg"
          >
            <span class="mr-3 md:hidden">Sous total :</span>
            {{ productTotalPrice }}€ HT
          </span>
          <span
            v-if="ecoTaxLine"
            class="mt-0.5 w-full rounded py-0.5 text-xs text-gray-700 md:w-auto"
          >
            dont {{ ecoTaxLine }}€ d'éco-part
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

import { PageList } from '@/vuejs/router'
import { formatPrice, getImage } from '@/vuejs/services/utils'

import sampleImg from '@/vuejs/assets/img/sample_product_img.png'

const props = defineProps({
  item: {
    required: true,
    type: Object,
  },
})

const productImage = computed((): string => {
  if (props.item.variant?.mainImageUrl) return props.item.variant.mainImageUrl
  if (props.item.variant?.product?.images?.[0])
    return props.item.variant.product.images[0]
  return getImage(sampleImg)
})

const productSlug = computed((): string => {
  return props.item.variant.product.externalId ?? ''
})

const productName = computed((): string => {
  return props.item.variant?.product?.name?.default || ''
})

const productReference = computed((): string => {
  return props.item.variant?.product?.reference || props.item.variant?.sku || ''
})

const productPrice = computed((): number | string => {
  return props.item.unit_price ? formatPrice(props.item.unit_price) : ''
})

const productTotalPrice = computed((): number | string => {
  return formatPrice(props.item.total_excluding_taxes / 100)
})

const productSeller = computed((): string => {
  return props.item.variant?.product?.seller?.name || ''
})

const ecoTaxLine = computed((): string | null => {
  const unitEcoTax = props.item.eco_tax
  if (!unitEcoTax) return null
  const total = unitEcoTax * (props.item.quantity ?? 1)
  return formatPrice(total)
})
</script>

<style scoped>
.input-qte {
  @apply rounded-lg border border-gray-300 px-0 text-center text-sm md:text-base lg:text-lg;
}
</style>
