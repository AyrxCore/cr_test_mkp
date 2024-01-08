<template>
  <DefaultModal background="bg-white" @cancel="onCancelClick">
    <template #content>
      <FormContact />
    </template>
  </DefaultModal>
</template>

<script lang="ts" setup>
import DefaultModal from '@/vuejs/modules/account/pages/DefaultModalPage.vue'
import FormContact from '@/vuejs/modules/contact/component/FormComponent.vue'

const emit = defineEmits(['cancel'])

const onCancelClick = () => {
  emit('cancel')
}
</script>

<style scoped></style>
