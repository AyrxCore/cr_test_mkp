<template>
  <div class="my-2 flex cursor-pointer items-center text-left text-sm">
    <input
      v-model="companyRadio"
      :value="props.company.id"
      :checked="props.company.checked"
      :id="`companyRadio-${props.company.id}`"
      name="companyRadio"
      type="radio"
      class="mr-3 cursor-pointer"
      @change="handleCompanySelection(props.company)"
    />
    <label :for="`companyRadio-${props.company.id}`" class="cursor-pointer">
      {{ props.company.name }} ({{ props.company.productCount }})
    </label>
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
