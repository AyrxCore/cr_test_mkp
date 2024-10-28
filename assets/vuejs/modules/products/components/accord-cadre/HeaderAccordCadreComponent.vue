<template>
  <div class="px-8">
    <BreadcrumbSharedComponent
      :list-url="breadcrumbUrl"
      :current-page="name"
      gtm-event-name="click_fat_breadcrumbs"
    />
    <div class="text-green my-4 flex flex-col lg:flex-row lg:items-center">
      <h3 class="text-title-primary">
        {{ name }}
      </h3>
      <div v-if="note" class="mt-2 flex flex-row items-center lg:ml-4 lg:mt-0">
        <LeafIconComponent class="mr-2 lg:ml-2" />
        <span class="text-md mr-2 flex font-bold text-green-qantis md:text-lg">
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
  </div>

  <div class="relative col-span-3 mt-5 px-0 md:px-8 lg:mt-0">
    <div
      class="h-[260px] bg-cover bg-center"
      :style="{ backgroundImage: `url(${bannerDesktop})` }"
    >
      <h3
        v-if="bannerText"
        class="sm:text-title-default-size flex h-full w-full items-center justify-center bg-black/50 px-6 text-center text-2xl font-bold text-white"
      >
        {{ bannerText }}
      </h3>
    </div>
  </div>
</template>
<script lang="ts" setup>
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import { computed, onBeforeMount, onUnmounted, ref } from 'vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import InformationIconComponent from '@/vuejs/modules/shared/icon/InformationIconComponent.vue'
import { ProductPageList } from '@/vuejs/router/pages-list'

const emit = defineEmits(['scroll-to'])

const props = defineProps({
  name: {
    required: true,
    type: String,
  },
  note: {
    required: false,
    type: String,
    default: null,
  },
  categories: {
    required: true,
    type: Object,
  },
  bannerDesktop: {
    required: true,
    type: String,
  },
  bannerText: {
    type: String,
  },
})

const windowWidth = ref<number>(null)

const breadcrumbUrl = computed(() => {
  const breadcrumb = []

  if (props.categories) {
    breadcrumb.push({
      id: props.categories[0].id,
      name: props.categories[0].name,
      url: {
        name: ProductPageList.PRODUCTS,
        query: { category: props.categories[0].id },
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
