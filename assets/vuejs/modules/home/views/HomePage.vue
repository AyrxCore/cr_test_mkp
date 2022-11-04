<template>
  <BaseTemplate
    title="Qantis - MarketPlace"
  >
    <div class="flex justify-center">
      <LoaderSharedComponent
        class="mt-4"
      />
      <slot/>
    </div>
  </BaseTemplate>
</template>
<script>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
export default {
  components: {LoaderSharedComponent, BaseTemplate}
}
</script>
