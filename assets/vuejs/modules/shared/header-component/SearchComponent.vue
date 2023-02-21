<template>
  <div ref="wrapper" class="flex w-full">
    <input
      v-model="searchTerm"
      class="input !rounded-r-none !p-2 !text-sm md:!text-base md:!text-lg lg:!px-8 lg:!py-4 truncate md:text-clip"
      name="search"
      placeholder="Rechercher un produit, un accord cadre ou un fournisseur"
      type="search"
      @keyup.enter="searchTerm"
    />
    <button
      class="flex items-center rounded-r-md px-3 py-1 text-white hover:opacity-75 bg-secondary"
      @click="searchProduct"
    >
      <SearchIconComponent />
    </button>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import SearchIconComponent from '@/vuejs/modules/shared/icon/SearchIconComponent.vue'
import { useRoute } from 'vue-router'
import { ProductPageList } from '@/vuejs/modules/products/routerProducts'

const route = useRoute()
const emit = defineEmits(['searchProduct'])
const wrapper = ref<HTMLElement>()

const searchTerm = ref('')

const searchProduct = (() => {
  emit('searchProduct', {term: searchTerm.value})
})

onMounted(() => {
  if (route.query.q && route.name === ProductPageList.PRODUCTS) {
    searchTerm.value = route.query.q
  }
})
</script>
