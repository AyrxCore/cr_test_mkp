<template>
  <breadcrumb-shared-component
    :list-url="breadcrumbUrl"
    :current-page="name"
  />
  <ContactUsButtonComponent />
  <div class="text-green mt-3.5 flex flex-col lg:flex-row lg:items-center">
    <h3 class="text-title-35 text-primary">
      {{ name }}
    </h3>
    <div
      v-if="note"
      class="flex flex-row"
    >
      <LeafIconComponent class="mx-2" />
      <span class="mr-2 flex text-sm font-bold md:text-lg text-green-qantis">{{ note }}</span>
      <span class="mt-1 flex text-xs text-gray-500 md:mt-2"
        >Selon notre référentiel RSE</span
      >
      <span
        class="flex cursor-pointer md:mt-1"
        title="Message explicatif du référentiel RSE"
        ><InformationIconComponent
      /></span>
    </div>
  </div>

  <div class="mt-10 flex flex-col md:grid md:grid-cols-4 md:gap-4">
    <div class="flex items-center justify-center rounded-lg bg-white">
      <img
        :src="logo"
        :alt="'Logo ' + name"
        class="items-center rounded-lg sm:mx-auto"
      />
    </div>
    <div class="col-span-3 mt-5 hidden rounded-lg bg-white md:mt-0 md:flex">
      <img
        :src="barner"
        :alt="'Bannière ' + name"
        class="items-center rounded-lg sm:mx-auto"
      />
    </div>
  </div>
</template>
<script lang="ts" setup>
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import { computed, ref } from 'vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import InformationIconComponent from '@/vuejs/modules/shared/icon/InformationIconComponent.vue'

const props = defineProps({
  name: {
    required: true,
    type: String,
  },
  note: {
    required: true,
    type: String,
  },
  logo: {
    required: true,
    type: String,
  },
  barner: {
    required: true,
    type: String,
  },
  categories: {
    required: true,
    type: Array,
  },
})

const breadcrumbUrl = computed(() => {
  const breadcrumb = []
  if (props.categories) {
    Object.entries(props.categories).forEach(([key, value], index) => {
      breadcrumb.push({
        id: key,
        name: value,
      })
    })
  }

  return breadcrumb
})

const listUrl = ref([
  {
    name: 'Catégories',
    url: '',
  },
  {
    name: 'Outillage',
    url: '',
  },
])
</script>

<style scoped></style>
