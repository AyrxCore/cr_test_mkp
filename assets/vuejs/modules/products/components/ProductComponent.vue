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
      <AddFavoriteComponent
        v-if="product.variants?.length === 2"
        :product-id="product.id"
        :product-name="product.name"
        :variant-id="variantId"
        :favorites-product="product.favorites"
      />
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
        @click="
          $router.push({
            name: ProductPageList.PRODUCT,
            params: { slug: productSlug },
          })
        "
      />
      <div
        v-else
        class="loading flex h-[116px] w-full items-center justify-center rounded-lg px-6 py-2"
      ></div>
    </div>
    <!-- Fin bloc image -->

    <!-- Bloc nom et description -->
    <div class="flex w-full flex-col justify-start">
      <h3
        class="truncate-custom truncate-custom-2 h-[55px] text-left text-sm font-bold text-gray-600 md:text-base lg:text-lg"
      >
        <RouterLink
          :to="{
            name: ProductPageList.PRODUCT,
            params: { slug: productSlug },
          }"
          >{{ product.name }}
        </RouterLink>
      </h3>
      <div class="h-[100px]">
        <p
          class="truncate-custom truncate-custom-3 mt-1 w-full justify-start text-left text-sm text-gray-400 md:text-base lg:text-lg"
          v-html="productDescription"
        />
      </div>
    </div>
    <!-- Fin bloc nom et description -->

    <!-- Bloc prix -->
    <div class="flex w-full items-center justify-start xl:mt-1">
      <span
        v-if="product.price"
        class="mr-2 text-sm font-bold text-primary md:text-base lg:text-lg"
      >
        {{ formatPrice(product.price) }}€
      </span>
      <span
        v-if="showLineThroughPrice"
        :class="{
          'text-sm text-gray-500 line-through md:text-base lg:text-lg':
            product.price,
          'text-sm font-bold text-primary md:text-base lg:text-lg':
            product.price === null,
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
          class="button border-2 border-secondary !text-secondary hover:!bg-white focus:!bg-white"
        >
          Voir les options
          <ArrowRightIconComponent class="ml-2 w-4 stroke-secondary" />
        </RouterLink>
      </div>

      <div v-else class="flex w-full justify-between">
        <div class="flex items-center justify-start">
          <span class="mr-2 text-sm text-gray-500">Qté :</span>
          <ProductQuantityComponent
            :quantity="quantity"
            @update-quantity="updateQuantity"
          />
        </div>
        <ButtonAddToCartComponent
          :product="product"
          :quantity="quantity"
          :variant-id="variantId"
        />
      </div>
    </div>
    <!-- Bloc quantité -->
  </div>
</template>
<script lang="ts" setup>
import { computed, PropType, ref } from 'vue'
import { formatPrice, getUpplerImage } from '@/vuejs/services/utils'
import { Product } from '@/vuejs/types/Product'
import { ProductPageList } from '@/vuejs/router/pages-list'
import ButtonAddToCartComponent from '@/vuejs/modules/shared/ButtonAddToCartComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import AddFavoriteComponent from '@/vuejs/modules/products/components/AddFavoriteComponent.vue'
import ProductQuantityComponent from '@/vuejs/modules/shared/ProductQuantityComponent.vue'
import { Variant } from '@/vuejs/types/Product/Variant'

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
</script>

<style scoped></style>
