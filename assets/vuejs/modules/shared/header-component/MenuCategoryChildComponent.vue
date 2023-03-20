<template>
    <h3
      class="mt-1 items-center text-left text-primary flex"
    >
      <RouterLink :to="{name: ProductPageList.PRODUCTS, query: { category: category.id, page: 1}}" replace>
        {{ props.category.name }}
      </RouterLink>
      <Chevron2RightIconComponent
        v-if="props.category.child"
        class="ml-2"
        :class="{
                'font-bold ml-2': props.category.parent === null,
                'mt-2 rotate-90 ease-in-out': showChildren
              }"
        @click="toggleChildren"
      />
    </h3>
    <div
      v-if="showChildren"
      class="ml-5"
    >
      <MenuCategoryChildComponent
        v-for="cat in props.category.child"
        :key="cat.id"
        :category="cat"
        :is-menu="isMenu"
      />
    </div>

</template>
<script lang="ts" setup>
import { ref } from 'vue'
import Chevron2RightIconComponent from '@/vuejs/modules/shared/icon/Chevron2RightIconComponent.vue'
import { ProductPageList } from '@/vuejs/router/pages-list'

const props = defineProps({
  category: {
    required: true,
    type: Object,
  },
  space: {
    type: Number,
    default: 0,
  },
  isMenu: {
    type: Boolean,
    default: false,
  }
})

const showChildren = ref<boolean>(false)

const toggleChildren = (() =>  {
  showChildren.value = !showChildren.value
})

</script>

<style scoped></style>
