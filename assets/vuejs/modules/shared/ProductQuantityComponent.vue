<template>
  <div class="skin-7">
    <div class="num-in">
      <span
        :class="{
          '!cursor-not-allowed': qte <= minQty,
        }"
        :disabled="qte <= minQty"
        :style="{
          color: betterTextColor('secondary'),
        }"
        class="minus rounded-l-md"
        @click="decrement"
        >-</span
      >
      <input
        v-model.number="qte"
        class="in-num"
        type="text"
        @blur="onBlur"
        @input="onInput"
      />
      <span
        :class="{
          '!cursor-not-allowed': qte >= maxQty,
        }"
        :disabled="qte >= maxQty"
        :style="{
          color: betterTextColor('secondary'),
        }"
        class="plus rounded-r-md"
        @click="increment"
        >+</span
      >
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, watch } from 'vue'
import { betterTextColor } from '@/vuejs/services/utils'

const emit = defineEmits(['updateQuantity', 'updateQuantityInput'])
const props = defineProps({
  quantity: {
    type: Number,
    required: true,
  },
  minQuantity: {
    type: Number,
    default: 1,
  },
  maxQuantity: {
    type: Number,
    default: 999,
  },
})

const minQty = computed(() => Math.max(1, props.minQuantity))
const maxQty = computed(() => props.maxQuantity)

const qte = ref<number|null>(Math.max(Math.max(1, props.minQuantity), props.quantity))

watch(
  () => props.quantity,
  (newQty) => {
    qte.value = Math.max(minQty.value, newQty)
  },
)

const onInput = (event: InputEvent): void => {
  const inputValue = (event.target as HTMLInputElement).value
  const onlyNumbers = inputValue.replace(/[^0-9]/g, '')
  if (onlyNumbers.length === 0) {
    qte.value = null
  } else if (parseInt(onlyNumbers) === 0) {
    qte.value = minQty.value
  } else if (parseInt(onlyNumbers) > maxQty.value) {
    qte.value = maxQty.value
  } else {
    qte.value = parseInt(onlyNumbers)
  }
  emit('updateQuantityInput', {
    quantity: qte.value,
  })
}

const onBlur = (): void => {
  if (!qte.value || qte.value < minQty.value) {
    qte.value = minQty.value
    emit('updateQuantity', { quantity: qte.value })
  } else if (qte.value > maxQty.value) {
    qte.value = maxQty.value
    emit('updateQuantity', { quantity: qte.value })
  } else if (qte.value !== props.quantity) {
    emit('updateQuantity', { quantity: qte.value })
  }
}

const decrement = (): void => {
  if (qte.value > minQty.value) {
    qte.value--
    emit('updateQuantity', {
      quantity: qte.value,
    })
  }
}

const increment = (): void => {
  if (qte.value < maxQty.value) {
    qte.value++
    emit('updateQuantity', {
      quantity: qte.value,
    })
  }
}
</script>
<style scoped>
/* skin 7 */

.skin-7 .num-in {
  @apply float-left w-[94px] rounded;
}

.skin-7 input.in-num {
  @apply float-left h-[30px] w-[81px] !border !border-secondary !p-0 text-center;
}

.skin-7 .num-in span {
  @apply float-left flex h-[30px] w-[24px] cursor-pointer items-center justify-center border border-x border-secondary bg-secondary text-center text-base text-white md:text-lg lg:text-xl;
  -webkit-transition: all 0.3s;
  -o-transition: all 0.3s;
  transition: all 0.3s;
}

.skin-7 .num-in input {
  float: left;
  width: 40px;
  line-height: 34px;
  text-align: center;
}
</style>
