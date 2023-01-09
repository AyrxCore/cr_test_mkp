<template>
    <div
      class="flex w-full items-center justify-between md:float-right lg:float-none lg:w-5/12"
    >
      <div class="text-center lg:mr-5 lg:w-2/12">
        <input
          :value="1"
          type="text"
          name="qte"
          class="w-10 rounded-lg border border-gray-300 text-center lg:w-14"
        />
      </div>
      <div class="lg:w-4/12">
        <span class="mt-2 text-sm text-gray-400 lg:text-lg line-through">
          <slot name="lineThroughPrice" />
        </span>
      </div>
      <div class="lg:w-4/12">
        <span class="mt-2 text-sm font-bold text-primary lg:text-lg">
          <slot name="price" />
        </span>
      </div>
      <div class="flex">
        <button class="flex text-gray-500">
          <HeartIconComponent class="mr-2 stroke-gray-500" />
        </button>
        <button class="flex text-gray-500">
          <TrashIconComponent :stroke-color="'#5E6875'" />
        </button>
      </div>
    </div>
</template>
<script lang="ts" setup>
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'

</script>
