<template>
  <div class="flex flex-col">
    <h3 class="text-lg text-primary md:mb-3 md:text-title-35">
      <slot name="name" />
    </h3>
    <h4 class="mb-1.5 text-sm font-bold text-gray-500 md:text-lg">
      Vendu par : <span class="uppercase"><slot name="partner" /></span>
    </h4>
    <span class="text-lg text-gray-500"
      >Référence : <slot name="reference"
    /></span>
  </div>
</template>
<script lang="ts" setup></script>

<style scoped></style>
