<template>
  <div
    class="mx-auto flex h-[466px] w-[392px] flex-col items-center justify-start rounded-md bg-white px-6 py-4 lg:h-[516px]"
  >
    <!-- Bloc header -->
    <div class="relative flex h-[50px] w-full items-center justify-between">
      <div
        class="flex h-[50px] w-[78px] items-center justify-start rounded-md bg-white"
      >
        <img
          :src="getUpplerImage(product.seller.avatar)"
          :alt="product.seller.name"
          class="h-full w-full object-contain"
        />
      </div>
      <div class="flex items-center">
        <div
          class="mr-4 rounded-sm bg-primary p-1 text-sm"
          :style="{
            color: betterTextColor('primary'),
          }"
        >
          Produit
        </div>
        <AddFavoriteComponent
          v-if="product.variants?.length === 2"
          :product-id="product.id"
          :product-name="product.name"
          :variant-id="variantId"
          :favorites-product="product.favorites"
        />
      </div>
    </div>
    <!-- Fin bloc header -->

    <!-- Bloc image -->
    <div
      class="mx-auto flex h-[139px] w-full items-center justify-center rounded-lg px-1 lg:h-[191px]"
    >
      <img
        v-if="product.images[0]"
        :src="getUpplerImage(product.images[0])"
        :alt="product.name"
        class="flex h-full cursor-pointer items-center lg:w-full lg:max-w-max"
        @click="goToProductPage('click_slider_home_products_img')"
      />
      <div
        v-else
        class="loading flex h-[116px] w-full items-center justify-center rounded-lg px-6 py-2"
      />
    </div>
    <!-- Fin bloc image -->

    <!-- Bloc nom et description -->
    <div class="flex w-full flex-col justify-start">
      <h3
        class="truncate-custom truncate-custom-2 h-[55px] text-left text-sm font-bold text-primary md:text-base lg:text-lg"
      >
        <RouterLink
          :to="{
            name: ProductPageList.PRODUCT,
            params: { slug: productSlug },
          }"
          @click="gtmEvent('click_slider_home_products_title')"
          >{{ product.name }}
        </RouterLink>
      </h3>
      <div class="h-[100px]">
        <p
          class="truncate-custom truncate-custom-3 mt-1 w-full justify-start text-left text-sm md:text-base lg:text-lg"
          v-html="productDescription"
        />
      </div>
    </div>
    <!-- Fin bloc nom et description -->

    <!-- Bloc prix -->
    <div class="flex w-full items-center justify-start xl:mt-1">
      <span v-if="product.price" class="mr-2 text-lg md:text-base lg:text-lg">
        {{ formatPrice(product.price) }}€
      </span>
      <span
        v-if="showLineThroughPrice"
        :class="{
          'text-sm line-through': product.price,
          'text-sm font-bold': product.price === null,
        }"
      >
        {{ formatPrice(product.priceReference) }}€ HT
      </span>
    </div>
    <!-- Fin bloc prix -->

    <!-- Bloc quantité -->
    <div class="mt-1 flex w-full justify-between">
      <div v-if="product.variants?.length > 2" class="mx-auto items-center">
        <RouterLink
          :to="{
            name: ProductPageList.PRODUCT,
            params: { slug: productSlug },
          }"
          class="button border-2 border-primary !text-primary shadow-none hover:scale-105 hover:!border-primary hover:!bg-white hover:!shadow-inner-darker focus:!bg-white"
        >
          Voir les options
          <ArrowRightIconComponent class="ml-2 w-4 stroke-primary" />
        </RouterLink>
      </div>

      <div v-else class="flex w-full justify-between">
        <div class="flex items-center justify-start">
          <ProductQuantityComponent
            :quantity="quantity"
            @update-quantity="updateQuantity"
          />
        </div>
        <ButtonAddToCartComponent
          :product="product"
          :quantity="quantity"
          :variant-id="variantId"
          @click="
            gtmEvent('click_slider_home_products_cta', {
              partenaire_name: product.seller.name,
              partenaire_id: product.seller.id,
              product_name: product.name,
              product_id: product.id,
              qty_value: product.quantity,
            })
          "
        />
      </div>
    </div>
    <!-- Bloc quantité -->
  </div>
</template>
<script lang="ts" setup>
import { computed, PropType, ref } from 'vue'

import ButtonAddToCartComponent from '@/vuejs/modules/shared/ButtonAddToCartComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import AddFavoriteComponent from '@/vuejs/modules/products/components/AddFavoriteComponent.vue'
import ProductQuantityComponent from '@/vuejs/modules/shared/ProductQuantityComponent.vue'

import {
  formatPrice,
  getUpplerImage,
  betterTextColor,
} from '@/vuejs/services/utils'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { Product } from '@/vuejs/types/Product'
import { Variant } from '@/vuejs/types/Product/Variant'
import { buildStandardGtmData, gtmMixinPushEvent } from '@/vuejs/services/gtm'
import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'
import router from '@/vuejs/router'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'

const userStore = useUserStore()
const channelStore = useChannelStore()

const currentChannel = channelStore.currentChannel

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const quantity = ref<number>(1)

const showLineThroughPrice = computed(() => {
  return (
    props.product.priceReference &&
    props.product.priceReference !== props.product.price
  )
})

const variantId = computed(() => {
  if (2 === props.product.variants?.length) {
    const variant = props.product.variants.filter(function (el: Variant) {
      return el.sku != null
    })
    return variant[0].id
  }
  return null
})

const productSlug = computed(() => {
  return props.product.slug
})

const productDescription = computed(() => {
  if (props.product.description?.length > 140) {
    return props.product.description.substring(0, 140) + '...'
  }
  return props.product.description
})

const updateQuantity = (event) => {
  quantity.value = event.quantity
}

const gtmEvent = (eventName: string, additionalData = null) => {
  let data = buildStandardGtmData(userStore.user['@id'], currentChannel.name)
  data = additionalData ? { ...data, ...additionalData } : data
  gtmMixinPushEvent(eventName, data)
}

const goToProductPage = (eventName: string) => {
  router.push({
    name: ProductPageList.PRODUCT,
    params: { slug: productSlug.value },
  })
  gtmEvent(eventName, {
    partenaire_name: props.product.seller.name,
    partenaire_id: props.product.seller.id,
    product_name: props.product.name,
    product_id: props.product.id,
  })
}
</script>

<style scoped></style>
