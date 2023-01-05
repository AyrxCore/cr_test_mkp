<template>
  <div class="bloc-item-favoris">
    <div class="md:w-5/12">
      <a
        href="/app/account/favoris-details"
        class="text-purple-600 underline"
        >{{ favorite.name }}</a
      >
    </div>
    <div class="md:w-2/12">
      {{ favorite.dateCreate }}
    </div>
    <div class="md:w-2/12">
      {{ favorite.dateUpdate }}
    </div>
    <div class="md:w-2/12">
      {{ favorite.numberArticle }}
      {{ favorite.numberArticle > 1 ? 'articles' : 'article' }}
    </div>
    <div class="flex justify-end md:w-1/12">
      <button><EditIconComponent class="mr-2" /></button>
      <button><TrashIconComponent :stroke-color="'#9866ff'" /></button>
    </div>
  </div>
</template>
<script lang="ts" setup>
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
const props = defineProps({
  favorite: {
    required: true,
    type: Object,
  },
})
</script>
<style scoped>
.bloc-item-favoris {
  @apply mb-2.5 flex w-[48.5%] flex-col rounded-lg bg-white p-2.5 text-sm text-gray-500 md:w-full md:flex-row md:text-base lg:text-lg;
}
</style>
