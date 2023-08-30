<template>
  <div ref="wrapper" class="flex w-full">
    <input
      v-model="searchTerm"
      class="input truncate !rounded-r-none !p-2 !text-sm md:text-clip md:!text-base md:!text-lg lg:!px-8 lg:!py-4"
      name="search"
      placeholder="Rechercher un produit, un accord-cadre ou un fournisseur"
      type="search"
      @keyup.enter="searchProduct"
    />
    <button
      class="flex items-center rounded-r-md bg-secondary px-3 py-1 text-white hover:opacity-75"
      @click="searchProduct"
    >
      <SearchIconComponent />
    </button>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, ref, watch } from 'vue'
import SearchIconComponent from '@/vuejs/modules/shared/icon/SearchIconComponent.vue'
import { useRoute } from 'vue-router'
import { ProductPageList } from '@/vuejs/router/pages-list'

const route = useRoute()
const emit = defineEmits(['searchProduct'])
const wrapper = ref<HTMLElement>()

const searchTerm = ref('')

const searchProduct = () => {
  emit('searchProduct', { term: searchTerm.value })
}

onMounted(() => {
  updateSearchTerm(route.query.q)
})

const updateSearchTerm = (query) => {
  if (query && route.name === ProductPageList.PRODUCTS) {
    searchTerm.value = query
  } else {
    searchTerm.value = null
  }
}

watch(
  () => route.query,
  async (routeObject) => {
    updateSearchTerm(routeObject.q)
  },

  { immediate: true },
)
</script>
