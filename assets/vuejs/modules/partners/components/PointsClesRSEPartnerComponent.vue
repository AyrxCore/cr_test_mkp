<template>
  <div class="mt-10 mt-5 flex flex-col lg:grid lg:grid-cols-3 lg:gap-4">
    <div class="rounded-lg bg-white p-4 text-gray-500 md:p-7.5 col-span-2 flex flex-col">
      <h3
        class="text-title-35 mb-[1.563rem] font-bold leading-9 text-primary lg:w-3/4"
      >
        Achats durables : <br />
        <span class="text-green-qantis">engagements RSE</span> du partenaire
        <div class="mt-2 flex flex-row items-center">
          <LeafIconComponent class="mr-2" />
          <span class="mr-2 flex font-bold text-green-qantis">{{ note }}</span>
        </div>
      </h3>
      <p
        class="text-sm md:text-base xl:text-lg"
        v-html="description"
      />
    </div>
    <div
      class="mt-5 items-center rounded-lg bg-white px-3 pt-11 text-sm text-gray-500 md:text-base lg:mt-0 xl:text-lg"
    >
      <ul>
        <li
          v-for="(pointRse, key) in pointsClesRse"
          :key="key"
          class="mb-7.5 items-center text-sm md:text-base xl:text-lg"
        >
              <span class="flex flex-row">
                <CheckCircleIconComponent
                  class="mr-2 !inline h-[20px] h-[28px] w-[20px] md:w-[24px] lg:mr-3.5"
                />
                <span class="w-full">
                  <span class="mr-1 font-bold"
                  >Point clé n° {{ key + 1 }} : </span
                  >{{ pointRse }}
                </span>
              </span>
        </li>
      </ul>
    </div>
  </div>
</template>
<script lang="ts" setup>
import CheckCircleIconComponent from '@/vuejs/modules/shared/icon/CheckCircleIconComponent.vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'

const props = defineProps({
  description: {
    type: String,
    required: true,
  },
  note: {
    type: String,
    default:null,
  },
  pointsClesRse: {
    type: Array,
    default: null,
  },
})

</script>

<style scoped></style>
