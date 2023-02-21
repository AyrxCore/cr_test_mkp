<template>
  <div
    class="mt-10 mt-5 flex flex-col text-gray-600 lg:grid lg:grid-cols-9 lg:gap-4"
  >
    <div
      class="rounded-lg bg-white px-4 pt-7.5 pb-4 text-sm md:text-base lg:col-span-5 xl:px-7.5 xl:text-lg"
    >
      <p v-html="description" />
    </div>
    <div
      class="mt-5 rounded-lg bg-white px-4 pt-7 pb-4 text-gray-500 lg:col-span-4 lg:mt-0"
    >
      <ul>
        <li
          v-for="(pointCle, key) in pointsCles"
          :key="key"
          class="mb-7.5 text-sm md:text-base xl:text-lg"
        >
              <span class="flex flex-row">
                <CheckIconComponent
                  class="mr-2 !inline h-[20px] h-[28px] w-[20px] md:w-[24px] lg:mr-3.5"
                />
                <span class="w-full">{{ pointCle }}
                </span>
              </span>
        </li>
      </ul>
    </div>
  </div>
</template>
<script lang="ts" setup>
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'

const props = defineProps({
  description: {
    type: String,
    required: true,
  },
  pointsCles: {
    type: Array,
    default: null,
  },
})


</script>

<style scoped></style>
