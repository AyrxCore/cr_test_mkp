<template>
  <div
    class="mb-3 flex flex-col justify-between rounded-lg bg-white py-6 px-6 text-gray-500 md:h-[147px]"
  >
    <div
      class="flex md:justify-end"
    >
      <span
        class="w-fit rounded-md px-2 md:py-1 text-white"
        :class="order.statut.color"
      >
        {{ order.statut.name }}
      </span>
    </div>
    <div class="text-sm md:text-base md:absolute mt-5 md:mt-0 lg:text-lg">
      <p class="flex mb-2.5">
        Nom de la commande:  {{ order.name }}
      </p>
      <p class="flex mb-2.5">
        Commande passé par: {{ order.buyer }}
      </p>
      <p class="flex">Date: {{ order.date }}</p>
    </div>
    <div class="flex md:justify-end mt-5 md:mt-0">
      <div class="">
        <a
          href="#"
          class="inline-flex items-center underline decoration-2 underline-offset-4 hover:decoration-purple-600"
        >
          Voir le détail de la commande
          <ArrowRightIconComponent :stroke-color="'#5E6875'" class="ml-2" />
        </a>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
const props = defineProps({
  order: {
    required: true,
    type: Object,
  },
})
</script>
