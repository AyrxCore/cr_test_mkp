import { ref } from 'vue'

export const status = ref({
  not_activated: 'NOT_ACTIVATED',
  pending: 'PENDING',
  activated: 'ACTIVATED',
})

export const filterType = {
  category: 'CATEGORY',
  property: 'PROPERTY',
  company: 'COMPANY',
  name: 'NAME',
}
