<template>
  <div class="flex h-[458px] flex-col">
    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="inline-block min-w-full sm:px-6 lg:px-8">
        <div class="overflow-hidden">
          <table class="min-w-full">
            <thead class="border-b bg-white">
              <tr class="bg-primary text-center text-white">
                <th scope="col" class="px-6 py-4 text-sm font-medium">
                  Modèle
                </th>
                <th scope="col" class="px-6 py-4 text-sm font-medium">
                  Remise
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(modele, key) in listeModeles"
                :key="key"
                :class="{
                  'border-b  bg-gray-100': !modulo(key),
                }"
                class="text-center text-gray-500"
              >
                <td class="whitespace-nowrap px-6 py-1.5 text-sm font-medium">
                  {{ modele.name }}
                </td>
                <td class="whitespace-nowrap px-6 py-1.5 text-sm font-light">
                  {{ modele.percentage }} %
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed } from 'vue'

const listeModeles = computed(() => {
  const modeles = []
  for (let i = 1; i < 12; i++) {
    const percent = Math.floor(Math.random() * 24)
    modeles.push({
      name: 'Modele ' + i,
      percentage: percent,
    })
  }

  return modeles
})

const modulo = (index) => {
  return index % 2 === 0
}
</script>

<style scoped></style>
