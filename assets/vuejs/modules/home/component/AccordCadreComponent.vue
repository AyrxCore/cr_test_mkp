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
      <div
        class="flex h-[150px] max-w-[200px] items-center justify-center rounded-lg sm:mx-auto sm:w-full md:h-[139px] lg:h-[191px]"
        @click="
          $emit('click-img', {
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
          <img
            :src="properties.logo_partenaire"
            :alt="`Image ${accord.name}`"
            class="max-h-[150px] cursor-pointer items-center sm:flex md:max-h-[139px] lg:max-h-[191px] lg:w-full lg:max-w-max"
          />
        </RouterLink>
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

        <div class="flex w-full items-center justify-start xl:mt-1">
          <p
            v-html="accord.description"
            class="description truncate-custom truncate-custom-3 mb-4 px-2"
          />
        </div>

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

import { betterTextColor } from '@/vuejs/services/utils'

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
