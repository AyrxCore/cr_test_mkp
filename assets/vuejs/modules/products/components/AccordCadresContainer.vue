<template>
  <div
    :class="[
      'mb-3 min-w-0 rounded-2xl bg-white p-2 shadow-sm min-[375px]:p-4 md:p-6',
      visibleAccordCadres.length <= FAT_DEFAULT_VISIBLE && 'pb-3',
    ]"
  >
    <div class="mb-4 flex items-center justify-between">
      <h4 class="text-lg font-semibold text-primary">
        Partenaires
        <span class="ml-1 text-sm font-normal text-gray-500 md:hidden"
          >({{ count }})</span
        >
        <span class="ml-1 hidden text-sm font-normal text-gray-500 md:inline"
          >({{ count }} résultat{{ count > 1 ? 's' : '' }})</span
        >
      </h4>
      <button
        v-if="accordCadres.length > FAT_DEFAULT_VISIBLE && !(props.defaultExpanded && fatExpanded)"
        type="button"
        class="group flex shrink-0 items-center gap-1.5 rounded-md px-2 py-1 text-sm font-medium text-primary transition-colors hover:bg-blue-50"
        @click="toggleFat"
      >
        <template v-if="!fatExpanded">
          <GridDotsIconComponent class="shrink-0" />
          <span>Voir tout</span>
        </template>
        <template v-else>
          <ChevronDownIconComponent class="w-4 h-4 shrink-0 rotate-180" />
          <span>Réduire</span>
        </template>
      </button>
    </div>

    <!-- Slider : ≤5 éléments, visible tant que l'écran ne peut pas afficher 5 cartes sur une ligne (< 2xl) -->
    <div
      v-if="visibleAccordCadres.length <= FAT_DEFAULT_VISIBLE"
      class="accord-cadres-slider relative mx-5 mt-1 block min-w-0 overflow-visible pb-4 md:mx-14 md:mt-5 2xl:hidden"
    >
      <CarouselListSharedComponent
        :breakpoints="{
          1450: { slidesPerView: 4, spaceBetween: 20 },
          1280: { slidesPerView: 3, spaceBetween: 20 },
          650: { slidesPerView: 2, spaceBetween: 20 },
          0: { slidesPerView: 1, spaceBetween: 20 },
        }"
        :pagination="true"
        :slides-per-view="1"
        :space-between="20"
        class="swiper-nav-outside"
      >
        <SwiperSlide
          v-for="accord in visibleAccordCadres"
          :key="`ac-${accord.id}`"
          class="relative flex! h-auto! items-stretch justify-center bg-transparent"
        >
          <AccordCadreComponent
            :accord="accord"
            class="h-full! w-full! shadow-sm"
            @show-showcase-modal="$emit('show-showcase-modal', accord)"
          />
        </SwiperSlide>
      </CarouselListSharedComponent>
    </div>
    <!-- Grid : pour 5+ toujours visible ; pour ≤5 éléments uniquement à partir de 2xl (5 cartes sur une ligne) -->
    <div
      :class="[
        'grid h-auto grid-cols-1 items-stretch justify-items-center gap-3 sm:grid-cols-2 md:gap-5 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5',
        visibleAccordCadres.length <= FAT_DEFAULT_VISIBLE && 'hidden 2xl:grid',
      ]"
    >
      <AccordCadreComponent
        v-for="accord in visibleAccordCadres"
        :key="`ac-${accord.id}`"
        :accord="accord"
        class="mt-3 h-full! w-full! shadow-sm md:mt-0"
        @show-showcase-modal="$emit('show-showcase-modal', accord)"
      />
    </div>

    <div v-if="fatExpanded && hasMoreFatToShow" class="mt-5 flex w-full justify-center">
      <ButtonComponent
        class="button button-primary-outline w-full md:w-1/2"
        @click="showMoreFat"
      >
        <span class="text-lg!">Charger plus de partenaires</span>
      </ButtonComponent>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import { SwiperSlide } from 'swiper/vue'

import router from '@/vuejs/router'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { Product } from '@/vuejs/types/Product'

import AccordCadreComponent from '@/vuejs/modules/home/component/AccordCadreComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import GridDotsIconComponent from '@/vuejs/modules/shared/icon/GridDotsIconComponent.vue'
import ChevronDownIconComponent from '@/vuejs/modules/shared/icon/ChevronDownIconComponent.vue'

const FAT_DEFAULT_VISIBLE = 5
const FAT_PAGE_SIZE = 36

const props = defineProps<{
  accordCadres: Product[]
  count: number
  defaultExpanded?: boolean
}>()

defineEmits<{
  'show-showcase-modal': [accord: Product]
}>()

const fatExpanded = ref(props.defaultExpanded ?? false)
const fatVisibleCount = ref(
  props.defaultExpanded
    ? Math.min(FAT_PAGE_SIZE, props.accordCadres.length)
    : FAT_DEFAULT_VISIBLE,
)

const visibleAccordCadres = computed(() =>
  props.accordCadres.slice(0, fatVisibleCount.value),
)

const hasMoreFatToShow = computed(
  () => fatVisibleCount.value < props.accordCadres.length,
)

const toggleFat = () => {
  if (fatExpanded.value) {
    fatExpanded.value = false
    fatVisibleCount.value = FAT_DEFAULT_VISIBLE
  } else {
    sendGtmEvent('see_all_search_cta_click', {
      link_text: 'Voir tout',
      origin_url: router.currentRoute.value.fullPath,
    })
    fatExpanded.value = true
    fatVisibleCount.value = Math.min(FAT_PAGE_SIZE, props.accordCadres.length)
  }
}

const showMoreFat = () => {
  fatVisibleCount.value = Math.min(
    fatVisibleCount.value + FAT_PAGE_SIZE,
    props.accordCadres.length,
  )
}
</script>
<style scoped>
@reference '@/style/main.css';

.accord-cadres-slider :deep(.swiper-pagination) {
  @apply relative mt-3;
  transform: none;
}
</style>

