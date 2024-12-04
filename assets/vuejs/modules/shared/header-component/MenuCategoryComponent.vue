<template>
  <div class="modal-overlay">
    <div
      v-if="modelValue"
      v-click-outside="closeMenu"
      class="!lg:h-auto c-scrollbar absolute left-0 top-0 z-10 flex h-[80vh] w-4/5 flex-col overflow-auto bg-white px-5 py-2.5 text-sm shadow sm:left-24 sm:top-36 sm:w-auto sm:rounded"
    >
      <div v-if="showAllCategories">
        <div class="mt-3">
          <CloseIcon
            class="cursor-pointer hover:text-secondary"
            @click.stop="closeMenu"
          />
        </div>
        <div class="my-4">
          <router-link
            :to="{ name: ProductPageList.CATEGORIES }"
            class="text-2xl font-bold tracking-wide hover:bg-gray-200"
          >
            Toutes les catégories
          </router-link>
        </div>
        <div
          v-for="category in categories"
          :key="category.id"
          class="w-[100%] items-center py-1 !text-base !leading-7"
        >
          <MenuCategoryChildComponent
            :category="category"
            @select-category="showSelectedCategoryChildrens($event)"
            @close-menu="closeMenu"
          />
        </div>
      </div>
      <div v-else>
        <div
          class="mt-3 flex cursor-pointer items-center"
          @click.stop="backMenuCategories"
        >
          <ChevronLeftIconComponent class="mr-4 hover:text-secondary" />
          <span class="cursor-pointer text-lg">Retour</span>
        </div>
        <div class="my-4 text-2xl font-bold tracking-wide">
          {{ selectedCategory.name }}
        </div>
        <h3>
          <RouterLink
            :to="{
              name: ProductPageList.PRODUCTS,
              query: { category: selectedCategory.id },
            }"
            class="w-[100%] py-1 !text-base !leading-9 underline"
            replace
            @click="closeMenu"
          >
            VOIR TOUT
          </RouterLink>
        </h3>
        <div class="w-[100%] py-1 !text-base !leading-9">
          <MenuCategoryChildComponent
            v-for="cat in selectedCategory.children"
            :key="cat.id"
            :category="cat"
            @close-menu="closeMenu"
            @select-category="showSelectedCategoryChildrens($event)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
import CloseIcon from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'
import MenuCategoryChildComponent from '@/vuejs/modules/shared/header-component/MenuCategoryChildComponent.vue'
import ChevronLeftIconComponent from '@/vuejs/modules/shared/icon/ChevronLeftIconComponent.vue'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { useCategoryStore } from '@/vuejs/stores/category'
import { Category } from '@/vuejs/types/Product/Category'

const emit = defineEmits(['update:modelValue'])

const props = defineProps({
  modelValue: {
    required: true,
    type: Boolean,
  },
})

const showAllCategories = ref<boolean>(true)
const selectedCategory = ref<Category>(null)

const categoryStore = useCategoryStore()

const categories = computed((): Category[] => {
  return categoryStore.categoriesSortedAlphabetically
})
const closeMenu = (): void => {
  emit('update:modelValue', false)
  backMenuCategories()
}

const showSelectedCategoryChildrens = (category) => {
  showAllCategories.value = false
  selectedCategory.value = category
}

const backMenuCategories = () => {
  if (selectedCategory.value && selectedCategory.value.parentId) {
    selectedCategory.value = categories.value.find(
      (c) => c.id === selectedCategory.value.parentId,
    )
  } else {
    showAllCategories.value = true
    selectedCategory.value = null
  }
}
</script>

<style scoped>
.c-scrollbar::-webkit-scrollbar {
  width: 12px;
}

.c-scrollbar::-webkit-scrollbar-track {
  @apply bg-white;
}

.c-scrollbar::-webkit-scrollbar-thumb {
  @apply bg-primary;
}

.c-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #9f9f9f;
}
</style>
