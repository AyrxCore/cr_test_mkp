<template>
  <div class="bloc-cart-saved">
    <div class="md:w-3/12">
      <a
        href="/app/account/favoris-details"
        class="text-purple-600 underline"
        >{{ cart.name }}</a
      >
    </div>
    <div class="md:w-2/12">
      {{ cart.dateCreate }}
    </div>
    <div class="md:w-3/12">
      {{ cart.numberArticle }}
      {{ cart.numberArticle > 1 ? 'articles' : 'article' }}
    </div>
    <div class="md:w-2/12">
      <span class="primary flex font-bold">{{ cart.total }}€ HT</span>
      <span class="flex text-gray-500">({{ cart.total }}€ HT)</span>
    </div>
    <div class="flex justify-between md:w-2/12">
      <a href="#" class="mr-3 underline">Détails</a>
      <div class="flex">
        <button class="flex"><EditIconComponent class="mr-2" /></button>
        <button class="flex">
          <TrashIconComponent :stroke-color="'#9866ff'" />
        </button>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
const props = defineProps({
  cart: {
    required: true,
    type: Object,
  },
})
</script>
<style scoped>
.bloc-cart-saved {
  @apply w-[48.5%] mb-2.5 text-gray-500 flex flex-col rounded-lg bg-white p-2.5 text-sm md:flex-row md:text-base lg:text-lg md:w-full;
}
</style>
