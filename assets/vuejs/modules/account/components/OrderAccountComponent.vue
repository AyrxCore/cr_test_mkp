<template>
  <div
    class="mb-3 flex flex-col justify-between rounded-lg bg-white py-6 px-6 text-gray-500 md:h-[147px]"
  >
    <div class="flex md:justify-end">
      <span
        class="w-fit rounded-md px-2 text-white md:py-1"
        :class="order.statut.color"
      >
        {{ order.statut.name }}
      </span>
    </div>
    <div class="mt-5 text-sm md:absolute md:mt-0 md:text-base lg:text-lg">
      <p class="mb-2.5 flex">Nom de la commande: {{ order.name }}</p>
      <p class="mb-2.5 flex">Commande passé par: {{ order.buyer }}</p>
      <p class="flex">Date: {{ order.date }}</p>
    </div>
    <div class="mt-5 flex md:mt-0 md:justify-end">
      <div>
        <a
          href="#"
          class="inline-flex items-center underline decoration-2 underline-offset-4 hover:decoration-purple-600"
        >
          Voir le détail de la commande
          <ArrowRightIconComponent class="ml-2 stroke-gray-500" />
        </a>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

defineProps({
  order: {
    required: true,
    type: Object,
  },
})
</script>
