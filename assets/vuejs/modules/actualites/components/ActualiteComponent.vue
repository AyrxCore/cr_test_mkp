<template>
  <div
    class="flex flex-col mx-auto h-[524px] items-center rounded-md bg-white px-6 py-2 "
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
    <div class="mt-5 justify-start w-full">
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
    <h3 class="text-left text-[25px] font-bold text-primary mt-6">
      <a href="/app/actualite">{{ actualite.title }}</a>
    </h3>
    <p class="mt-1 text-left text-lg text-gray-400">
      {{ actualite.description }}
    </p>
    <div class="flex w-full bottom-0 h-full items-end">
      <a
        href="/app/actualite"
        class="bottom-0 items-center text-sm font-medium text-primary underline "
      >
        Lire l'article
      </a>
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
