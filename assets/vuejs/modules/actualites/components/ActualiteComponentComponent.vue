<template>
  <div class="items-center mx-auto px-6 bg-white  py-2 rounded-md text-cotext h-full" data-category="{{actualite.categorie.id}}">
    <div class="items-center mx-auto ">
      <a href="/app/product">
        <img :src="actualite.img" alt="Image produit" class="mx-auto h-[205px!important] w-[334px!important]"/>
      </a>
    </div>
    <div >
      <p
        class="px-2 py-1 rounded-md w-max mb-3 text-white"
        :class="'bg-[' + actualite.categorie.color +']'"
      >
        {{actualite.categorie.name}}
      </p>
      <span class="text-gray-500">
        {{actualite.date}}
      </span>
    </div>
    <h3 class="text-left primary font-bold text-[25px] text-cotext">
      <a href="/app/actualite">{{actualite.name}}</a>
    </h3>
    <p class="text-left text-gray-400 mt-1 text-lg text-cotext">{{actualite.description}}</p>
    <a href="/app/actualite" class="flex items-center bottom-0 text-sm font-medium underline primary">
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
