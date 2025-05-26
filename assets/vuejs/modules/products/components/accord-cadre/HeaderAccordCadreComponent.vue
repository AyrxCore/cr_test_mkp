<template>
  <div class="px-8">
    <BreadcrumbSharedComponent
      :current-page="accord.name"
      :list-url="breadcrumbUrl"
      gtm-event-name="click_fat_breadcrumbs"
    />
    <div
      class="text-green my-4 flex flex-col justify-between lg:flex-row lg:items-center"
    >
      <div>
        <h3 class="text-title-primary">
          {{ accord.name }}
        </h3>
        <div
          v-if="note"
          class="mt-2 flex flex-row items-center lg:ml-4 lg:mt-0"
        >
          <LeafIconComponent class="mr-2 lg:ml-2" />
          <span
            class="text-md mr-2 flex font-bold text-green-qantis md:text-lg"
          >
            {{ note }}
          </span>
          <span class="flex text-sm"> Selon notre référentiel RSE </span>
          <span
            class="flex cursor-pointer items-center"
            title="Pour des achats plus responsables, nous notons nos partenaires à l’aide de notre référentiel RSE (Responsabilité Sociétale des Entreprises)"
            @click.prevent="scrollTo"
          >
            <InformationIconComponent class="ml-1 text-gray-500" />
          </span>
        </div>
      </div>
      <div class="flex">
        <AddFavoriteComponent
          :favorites-product="accord.favorites"
          :product-id="accord.id"
          :product-name="accord.name"
          class="ml-4"
          has-favorite-label
        />
      </div>
    </div>
  </div>

  <div class="relative col-span-3 mt-5 px-0 md:px-8 lg:mt-0">
    <div
      :style="{
        backgroundImage: `url(${accord.properties.banniere_partenaire})`,
      }"
      class="h-[260px] bg-cover bg-center"
    >
      <h3
        v-if="accord.properties.texte_banniere"
        class="sm:text-title-default-size flex h-full w-full items-center justify-center bg-black/50 px-6 text-center text-2xl font-bold text-white"
      >
        {{ accord.properties.texte_banniere }}
      </h3>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed, onBeforeMount, onUnmounted, PropType, ref } from 'vue'
import { ProductPageList } from '@/vuejs/router/pages-list'
import AddFavoriteComponent from '@/vuejs/modules/products/components/AddFavoriteComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import InformationIconComponent from '@/vuejs/modules/shared/icon/InformationIconComponent.vue'
import { Product } from '@/vuejs/types/Product'

const emit = defineEmits(['scroll-to'])

const props = defineProps({
  accord: {
    required: true,
    type: Object as PropType<Product>,
  },
  note: {
    required: false,
    type: String,
    default: null,
  },
})

const windowWidth = ref<number>(null)

const breadcrumbUrl = computed(() => {
  const breadcrumb = []

  if (props.accord.categories) {
    breadcrumb.push({
      id: props.accord.categories[0].id,
      name: props.accord.categories[0].name,
      url: {
        name: ProductPageList.PRODUCTS,
        query: { category: props.accord.categories[0].id },
      },
    })
  }

  return breadcrumb
})

const scrollTo = () => {
  emit('scroll-to')
}

const onResize = () => {
  windowWidth.value = window.innerWidth
}

onBeforeMount(() => {
  windowWidth.value = window.innerWidth
  window.addEventListener('resize', onResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', onResize)
})
</script>

<style scoped></style>
