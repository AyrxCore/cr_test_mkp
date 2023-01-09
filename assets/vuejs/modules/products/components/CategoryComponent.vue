<template>
  <div class="mx-auto flex flex-col rounded-md bg-white px-6 py-2">
    <div class="mx-auto items-center">
      <a href="/app/actualite">
        <img
          :src="category.image"
          alt="Image catégorie"
          class="mx-auto h-[247px!important] w-[393px!important]"
        />
      </a>
    </div>
    <h3
      class="mt-6 inline items-center text-left text-sm font-bold text-primary md:text-base lg:text-lg xl:text-[22px]"
    >
      <a href="#">{{ category.name }}</a>
      <ChevronRightIconComponent
        class="ml-1 inline cursor-pointer stroke-primary stroke-[3px] transition ease-in"
        :class="{ 'mt-4 rotate-90 ease-in-out': showSection === category.id }"
        @click="showSection = showSection === category.id ? -1 : category.id"
      />
    </h3>
    <span
      v-if="category.child"
      v-for="(children, keyChildren) in category.child"
      v-show="showSection === category.id"
      :key="keyChildren"
      class="ml-3 text-sm text-primary lg:text-base xl:text-lg"
    >
      <span class="inline items-center">
        <a href="#">{{ children.name }}</a>
        <ChevronRightIconComponent
          class="ml-5 inline cursor-pointer stroke-primary transition ease-in"
          :class="{
            'mt-3 rotate-90 ease-in-out': showSubSection === keyChildren,
          }"
          @click="
            showSubSection = showSubSection === keyChildren ? -1 : keyChildren
          "
        />
      </span>
      <span
        v-if="children.child"
        v-for="(subChildren, keySubChildren) in children.child"
        v-show="showSubSection === keyChildren"
        :key="keySubChildren"
        class="ml-3 flex items-center"
      >
        <a href="#">{{ subChildren.name }}</a>
      </span>
    </span>
  </div>
</template>
<script lang="ts" setup>
import ChevronRightIconComponent from '@/vuejs/modules/shared/icon/ChevronRightIconComponent.vue'
import { ref } from 'vue'

const props = defineProps({
  category: {
    required: true,
    type: Object,
  },
})

const showSection = ref<number>()
const showSubSection = ref<number>()
</script>

<style scoped></style>
