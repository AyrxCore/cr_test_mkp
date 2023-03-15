<template>
    <h3
      class="mt-6 inline items-center text-left text-primary flex"
      :class="{
              'text-sm font-bold md:text-base lg:text-lg xl:text-[22px]': props.category.parent === null,
              'flex flex-row-reverse justify-end': props.category.parent !== null
            }"
    >
      <RouterLink :to="{name: ProductPageList.PRODUCTS, query: { category: category.id, page: 1}}" replace>
        {{ props.category.name }}
      </RouterLink>
      <Chevron2RightIconComponent
        v-if="props.category.child"
        :class="{
                'w-10 font-bold h-5 ml-2': props.category.parent === null,
                'mr-2': props.category.parent !== null,
                'mt-4 rotate-90 ease-in-out': showChildren
              }"
        @click="toggleChildren"
      />
    </h3>
    <div
      v-if="showChildren"
      class="ml-5"
    >
      <CategoryComponent
        v-for="cat in props.category.child"
        :key="cat.id"
        :category="cat"
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
  }
})

const showChildren = ref<boolean>(false)

const toggleChildren = (() =>  {
  showChildren.value = !showChildren.value
})

</script>

<style scoped></style>
