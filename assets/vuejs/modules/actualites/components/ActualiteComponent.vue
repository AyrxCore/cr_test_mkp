<template>
  <div
    class="mx-auto h-full items-center rounded-md bg-white px-6 py-2"
    data-category="{{actualite.categorie.id}}"
  >
    <div class="mx-auto items-center">
      <a href="/app/product">
        <img
          :src="actualite.img"
          alt="Image produit"
          class="mx-auto h-[205px!important] w-[334px!important]"
        />
      </a>
    </div>
    <div>
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
    <h3 class="text-left text-[25px] font-bold text-primary">
      <a href="/app/actualite">{{ actualite.name }}</a>
    </h3>
    <p class="mt-1 text-left text-lg text-gray-400">
      {{ actualite.description }}
    </p>
    <a
      href="/app/actualite"
      class="bottom-0 flex items-center text-sm font-medium text-primary underline"
    >
      Lire l'article
    </a>
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
