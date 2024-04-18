<template>
  <div class="flex flex-col items-center rounded-md bg-white p-4">
    <div class="flex h-[50px] w-full items-center justify-end">
      <div
        class="rounded-sm bg-secondary p-1 text-sm"
        :style="{
          color: betterTextColor('secondary'),
        }"
      >
        Accord-cadre
      </div>
    </div>
    <div class="flex h-full w-full flex-col items-center">
      <div
        class="flex h-[150px] max-w-[200px] items-center justify-center rounded-lg sm:mx-auto sm:w-full md:h-[139px] lg:h-[191px]"
        @click="
          $emit('click-img', {
            partenaire_name: accord.seller.name,
            partenaire_id: accord.seller.id,
          })
        "
      >
        <img
          :src="properties.logo_partenaire"
          alt="Image produit"
          class="max-h-[150px] cursor-pointer items-center sm:flex md:max-h-[139px] lg:max-h-[191px] lg:w-full lg:max-w-max"
        />
      </div>

      <div class="flex h-full flex-col justify-between">
        <div class="flex w-full flex-col justify-start">
          <h3
            class="truncate-custom truncate-custom-2 my-2 text-center text-3xl font-bold text-primary md:text-xl lg:text-lg"
            @click="
              $emit('click-title', {
                partenaire_name: accord.seller.name,
                partenaire_id: accord.seller.id,
              })
            "
          >
            {{ accord.name }}
          </h3>
        </div>

        <div class="flex w-full items-center justify-start xl:mt-1">
          <p
            class="description truncate-custom truncate-custom-3 mb-4 px-2"
            v-html="accord.description"
          />
        </div>

        <div class="mt-1 flex w-full justify-center">
          <RouterLink
            :to="{
              name: ProductPageList.ACCORD_CADRE,
              params: { slug: accord.slug },
            }"
            class="button button-primary text-wrap mt-auto flex justify-center"
            :style="{
              color: betterTextColor('primary'),
            }"
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

import { betterTextColor } from '@/vuejs/services/utils'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

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

<style>
.text-wrap {
  text-wrap: wrap;
  text-align: center;
}
</style>
