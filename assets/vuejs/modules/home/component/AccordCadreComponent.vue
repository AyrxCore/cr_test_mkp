<template>
  <div class="flex flex-col items-center rounded-md bg-white p-4">
    <div class="flex h-[50px] w-full items-center justify-end">
      <div
        :style="{
          color: betterTextColor('secondary'),
        }"
        class="rounded-sm bg-secondary p-1 text-sm"
      >
        Accord-cadre
      </div>
    </div>

    <div class="flex h-full w-full flex-col items-center">
      <!-- Bloc image -->
      <div class="my-1 flex w-full items-center">
        <div
          class="flex h-[200px] max-w-[200px] items-center justify-center rounded-lg sm:mx-auto"
        >
          <RouterLink
            :to="{
              name: ProductPageList.ACCORD_CADRE,
              params: { slug: accord.slug },
            }"
            class="block"
          >
            <img
              :alt="`Image ${accord.name}`"
              :src="properties.logo_partenaire"
              class="max-h-[150px] cursor-pointer items-center sm:flex md:max-h-[139px] lg:max-h-[191px] lg:w-full lg:max-w-max"
            />
          </RouterLink>
        </div>
      </div>
      <!-- Fin bloc image -->

      <div class="flex h-3/5 w-full flex-col justify-between">
        <!-- Bloc titre -->
        <div class="h-[30%]">
          <h3
            class="truncate-custom truncate-custom-2 text-title-default-size my-2 text-center font-bold text-primary md:text-xl lg:text-lg"
            @click="
              $emit('click-title', {
                partenaire_name: accord.seller.name,
                partenaire_id: accord.seller.id,
              })
            "
          >
            <RouterLink
              :to="{
                name: ProductPageList.ACCORD_CADRE,
                params: { slug: accord.slug },
              }"
              class="block"
            >
              {{ accord.name }}
            </RouterLink>
          </h3>
        </div>
        <!-- Fin bloc titre -->

        <!-- Bloc description -->
        <div>
          <p
            class="description truncate-custom truncate-custom-3 mb-4 px-2 text-center"
            v-html="accord.description"
          />
        </div>
        <!-- Fin bloc description -->

        <div class="mt-1 flex w-full justify-center">
          <RouterLink
            :style="{
              color: betterTextColor('primary'),
            }"
            :to="{
              name: ProductPageList.ACCORD_CADRE,
              params: { slug: accord.slug },
            }"
            class="button button-primary"
            @click="
              $emit('click-cta', {
                partenaire_name: accord.seller.name,
                partenaire_id: accord.seller.id,
              })
            "
          >
            Consulter l'accord&#8209;cadre
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'

import { ProductPageList } from '@/vuejs/router/pages-list'
import { Product } from '@/vuejs/types/Product'

import {
  betterTextColor,
  formatPrice,
  getUpplerImage,
} from '@/vuejs/services/utils'
import ProductQuantityComponent from '@/vuejs/modules/shared/ProductQuantityComponent.vue'
import ButtonAddToCartComponent from '@/vuejs/modules/shared/ButtonAddToCartComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

const props = defineProps({
  accord: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const emit = defineEmits(['click-cta', 'click-title', 'click-img'])

const properties = computed(() => {
  return props.accord.properties
})
</script>
