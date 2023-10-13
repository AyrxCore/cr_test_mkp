<template>
  <div class="mt-2 flex items-center text-left text-sm text-primary">
    <input
      v-model="companyRadio"
      name="companyRadio"
      type="radio"
      :value="props.company.id"
      class="mr-1"
      :checked="props.company.checked"
      @change="handleCompanySelection(props.company)"
    />
    <label> {{ props.company.name }} ({{ props.company.productCount }}) </label>
  </div>
</template>
<script lang="ts" setup>
import { ref } from 'vue'

const props = defineProps({
  company: {
    required: true,
    type: Object,
  },
})

const companyRadio = ref()

const emit = defineEmits(['change-company'])

const handleCompanySelection = async (company) => {
  await emit('change-company', { company_id: company.id })
}
</script>

<style scoped></style>
