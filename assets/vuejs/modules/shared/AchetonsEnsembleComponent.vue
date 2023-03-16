<template>
  <div>
    <h2 class="text-primary xl:text-4xl text-2xl font-bold text-left">
      <span class="bg-gradient-to-r bg-clip-text from-secondary to-gradient-1 text-transparent">
        Depuis plus de 20 ans,
      </span><br/>
      nous achetons mieux ensemble
    </h2>
    <div class="flex flex-wrap lg:flex-nowrap lg:mt-8 mt-8 lg:space-x-2">
      <div class="lg:pr-0 pr-2 w-1/2 lg:w-auto text-left">
        <LayersIconComponent class="flex h-[44px] w-[44px]" />
        <h3 class="text-gray-500 text-xl font-bold mt-6">Réalisez 27% d'économies</h3>
        <div class="text-gray-400">Bénéficiez d'avantages comparables à ceux des grands groupes</div>
      </div>
      <div class="pl-2 w-1/2 lg:w-auto text-left">
        <FolderSearchIconComponent class="flex h-[40px] w-[45px]" />
        <h3 class="text-gray-500 text-xl font-bold mt-6">Gagnez du temps</h3>
        <div class="text-gray-400">Restez concentré sur votre coeur de métier</div>
      </div>
      <div class="lg:pl-2 lg:pr-0 pr-2 w-1/2 lg:w-auto text-left lg:mt-0 mt-6">
        <ExpandIconComponent class="flex h-[40px] w-[41px]" />
        <h3 class="text-gray-500 text-xl font-bold mt-6">Structurez et optimisez vos achats</h3>
        <div class="text-gray-400">Déployez des accords-cadres déjà négociés, simplement</div>
      </div>
      <div class="pl-2 w-1/2 lg:w-auto text-left lg:mt-0 mt-6">
        <LeafIconComponent class="flex h-[40px] w-[38px]" />
        <h3 class="text-gray-500 text-xl font-bold mt-6">Commencez votre démarche RSE par vos achats</h3>
        <div class="text-gray-400">Nous notons nos fournisseurs à l'aide d'un référentiel RSE</div>
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
