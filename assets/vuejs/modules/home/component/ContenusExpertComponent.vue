<template>
  <div class="mt-5 grid md:grid-cols-3 gap-4 p-8">
    <div
      v-for="(contenu, id) in contenus"
      :key="id"
      class="grid grid-cols-2"
    >
      <div class="flex rounded-lg border-[#F3EDFE]">
        <img
          :src="contenu.img"
          alt="Picture"
          class="items-center sm:mx-auto"
        />
      </div>
      <div class="px-6 text-left">
        <h3 class="text-[23px] font-bold text-primary">
          {{ contenu.titre }}
        </h3>
        <p class="my-4">
          <a
            href="#"
            class="mr-2 mb-2 w-auto items-center rounded-md bg-purple-600 px-5 py-2.5 text-sm text-white"
          >
            {{ contenu.btnNam }}
          </a>
        </p>
        <p class="text-lg">
          {{ contenu.desc }}
        </p>
        <div class="bottom-0">
          <a
            href="/app/actualite"
            class="bottom-0 flex items-center text-sm font-medium text-primary underline"
          >
            Lire l'article
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>

const props = defineProps({
  contenus: {
    required: true,
    type: Array,
  },
})
</script>

<style scoped>

</style>
