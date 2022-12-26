<template>
  <div
    class="mx-auto flex h-[524px] flex-col items-center rounded-md bg-white px-6 py-2"
    data-category="{{actualite.categorie.id}}"
  >
    <div class="mx-auto items-center">
      <a href="/app/actualite">
        <img
          :src="actualite.img_landscape"
          alt="Image produit"
          class="mx-auto h-[205px!important] w-[334px!important]"
        />
      </a>
    </div>
    <div class="mt-5 w-full justify-start">
      <p
        class="mb-3 w-max rounded-md px-2 py-1 text-white"
        :class="actualite.categorie.color"
      >
        {{ actualite.categorie.name }}
      </p>
      <span class="text-gray-500">
        {{ actualite.date }}
      </span>
    </div>
    <h3 class="mt-6 text-left text-[25px] font-bold text-primary">
      <RouterLink :to="{ path: '/app/actualite' }">{{
        actualite.title
      }}</RouterLink>
    </h3>
    <p class="mt-1 text-left text-lg text-gray-400">
      {{ actualite.description }}
    </p>
    <div class="bottom-0 flex h-full w-full items-end">
      <RouterLink
        :to="{ path: '/app/actualite' }"
        class="bottom-0 items-center text-sm font-medium text-primary underline"
      >
        Lire l'article
      </RouterLink>
    </div>
  </div>
</template>
<script lang="ts" setup>
const props = defineProps({
  actualite: {
    required: true,
    type: Object,
  },
})
</script>

<style scoped></style>
