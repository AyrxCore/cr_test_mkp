<template>
  <div>
    <h2 class="text-left text-2xl font-bold text-primary xl:text-4xl">
      <span
        class="bg-gradient-to-r from-secondary to-gradient-1 bg-clip-text text-transparent"
      >
        Depuis 2001, </span
      ><br />
      nous achetons mieux ensemble
    </h2>
    <div class="mt-8 flex flex-wrap lg:mt-8 lg:flex-nowrap lg:space-x-2">
      <div class="w-1/2 pr-2 text-left lg:w-auto lg:pr-0">
        <LayersIconComponent class="flex h-[44px] w-[44px]" />
        <h3 class="mt-6 text-xl font-bold text-gray-500">
          Réalisez 27% d'économies
        </h3>
        <div class="text-gray-400">
          Bénéficiez d'avantages comparables à ceux des grands groupes
        </div>
      </div>
      <div class="w-1/2 pl-2 text-left lg:w-auto">
        <FolderSearchIconComponent class="flex h-[40px] w-[45px]" />
        <h3 class="mt-6 text-xl font-bold text-gray-500">Gagnez du temps</h3>
        <div class="text-gray-400">
          Restez concentré sur votre coeur de métier
        </div>
      </div>
      <div class="mt-6 w-1/2 pr-2 text-left lg:mt-0 lg:w-auto lg:pl-2 lg:pr-0">
        <ExpandIconComponent class="flex h-[40px] w-[41px]" />
        <h3 class="mt-6 text-xl font-bold text-gray-500">
          Structurez et optimisez vos achats
        </h3>
        <div class="text-gray-400">
          Déployez des accords-cadres déjà négociés, simplement
        </div>
      </div>
      <div class="mt-6 w-1/2 pl-2 text-left lg:mt-0 lg:w-auto">
        <LeafIconComponent class="flex h-[40px] w-[38px]" />
        <h3 class="mt-6 text-xl font-bold text-gray-500">
          Commencez votre démarche RSE par vos achats
        </h3>
        <div class="text-gray-400">
          Nous notons nos fournisseurs à l'aide d'un référentiel RSE
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import ExpandIconComponent from '@/vuejs/modules/shared/icon/ExpandIconComponent.vue'
import LeafIconComponent from '@/vuejs/modules/shared/icon/LeafIconComponent.vue'
import FolderSearchIconComponent from '@/vuejs/modules/shared/icon/FolderSearchIconComponent.vue'
import LayersIconComponent from '@/vuejs/modules/shared/icon/LayersIconComponent.vue'
</script>
