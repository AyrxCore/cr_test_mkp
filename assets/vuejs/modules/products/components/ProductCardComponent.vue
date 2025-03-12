<template>
  <div
    class="mb-2 flex min-h-[490px] flex-col items-center rounded-md bg-white p-4 md:mb-0"
  >
    <!-- Bloc header -->
    <div class="flex h-[50px] w-full items-center justify-between">
      <div
        class="flex h-[50px] w-[78px] items-center justify-start rounded-md bg-white"
      >
        <img
          :alt="product.seller.name"
          :src="getUpplerImage(product.seller.avatar)"
          class="h-full w-full object-contain"
        />
      </div>
      <div class="flex items-center">
        <div
          v-if="showProductDiscount"
          :style="{
            color: betterTextColor('primary'),
          }"
          class="text-md rounded-md bg-secondary px-2 py-1"
        >
          -{{ product.percent }}%
        </div>
        <AddFavoriteComponent
          v-if="product.variants?.length === 2"
          :favorites-product="product.favorites"
          :product-id="product.id"
          :product-name="product.name"
          :variant-id="variantId"
          class="ml-4"
        />
      </div>
    </div>
    <!-- Fin bloc header -->

    <div class="flex h-full w-full flex-col items-center">
      <!-- Bloc image -->
      <div class="my-1 flex w-full items-center">
        <div
          class="mx-auto flex h-[200px] max-w-[200px] items-center justify-center rounded-lg"
        >
          <img
            v-if="product.images[0]"
            :alt="product.name"
            :src="getUpplerImage(product.images[0])"
            class="flex h-full w-full cursor-pointer items-center object-contain lg:max-w-max"
            @click="goToProductPage"
          />
          <div
            v-else
            class="loading flex h-[116px] w-full items-center justify-center rounded-lg px-6 py-2"
          />
        </div>
      </div>
      <!-- Fin bloc image -->

      <!-- Bloc texte -->
      <div class="flex h-3/5 w-full flex-col justify-between">
        <!-- Bloc titre -->
        <div class="h-[25%]">
          <h3
            class="truncate-custom truncate-custom-2 text-left text-sm font-bold text-primary md:text-base lg:text-lg"
          >
            <RouterLink
              :to="{
                name: ProductPageList.PRODUCT,
                params: { slug: productSlug },
              }"
              @click="
                $emit('click-title', {
                  partenaire_name: props.product.seller.name,
                  partenaire_id: props.product.seller.id,
                  product_name: props.product.name,
                  product_id: props.product.id,
                  qty_value: quantity,
                })
              "
            >
              {{ product.name }}
            </RouterLink>
          </h3>
        </div>
        <!-- Fin bloc titre -->

        <!-- Bloc nom partenaire -->
        <div class="mt-1 h-[10%] text-primary">
          {{ product.seller.name }}
        </div>

        <!-- Fin bloc nom partenaire -->

        <!-- Bloc description -->
        <div class="h-[35%]">
          <p
            class="truncate-custom truncate-custom-3 mt-1 w-full justify-start text-left text-sm md:text-base lg:text-lg"
            v-html="productDescription"
          />
        </div>
        <!-- Fin bloc description -->

        <!-- Bloc prix -->
        <div class="my-1 flex h-[10%] w-full items-center justify-start">
          <span
            v-if="product.price"
            class="mr-2 text-lg font-bold text-primary md:text-base lg:text-lg"
          >
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
        <div class="mb-1 flex h-[20%] justify-end">
          <div class="mt-1 flex w-full justify-between">
            <div
              v-if="product.variants?.length > 2"
              class="mx-auto items-center"
            >
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
                  $emit('click-add-cart', {
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
        </div>
        <!-- Fin bloc quantité -->
        <!-- Fin bloc texte -->
      </div>
    </div>
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

import router from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'

import { Product } from '@/vuejs/types/Product'
import { Variant } from '@/vuejs/types/Product/Variant'

const emit = defineEmits([
  'click-add-cart',
  'click-title',
  'click-img',
  'click-moins-qty',
  'click-plus-qty',
])

const props = defineProps({
  product: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const quantity = ref<number>(1)

const showLineThroughPrice = computed((): boolean => {
  return (
    props.product.priceReference &&
    props.product.priceReference !== props.product.price
  )
})

const variantId = computed((): string | null => {
  if (2 === props.product.variants?.length) {
    const variant = props.product.variants.filter(function (el: Variant) {
      return el.sku != null
    })
    return variant[0].id
  }
  return null
})

const productSlug = computed((): string => {
  return props.product.slug
})

const showProductDiscount = computed((): boolean => {
  return props.product.percent > 0
})

const productDescription = computed((): string => {
  if (props.product.description?.length > 140) {
    return props.product.description.substring(0, 140) + '...'
  }
  return props.product.description
})

const updateQuantity = (event) => {
  const eventName =
    quantity.value > event.quantity ? 'click-moins-qty' : 'click-plus-qty'
  emit(eventName)
  quantity.value = event.quantity
}

const goToProductPage = () => {
  router.push({
    name: ProductPageList.PRODUCT,
    params: { slug: productSlug.value },
  })

  emit('click-img', {
    partenaire_name: props.product.seller.name,
    partenaire_id: props.product.seller.id,
    product_name: props.product.name,
    product_id: props.product.id,
    qty_value: quantity.value,
  })
}
</script>
