<template>
  <div class="num-block skin-7">
    <div class="num-in">
      <span
        class="minus dis rounded-l-md"
        :disabled="qte == 1"
        :class="{
          '!cursor-not-allowed': qte == 1,
        }"
        :style="{
          color: betterTextColor('secondary'),
        }"
        @click="decrement"
        >-</span
      >
      <input
        v-model.number="qte"
        type="text"
        class="in-num"
        @input="onInput"
        @blur="onBlur"
      />
      <span
        class="plus rounded-r-md"
        :style="{
          color: betterTextColor('secondary'),
        }"
        @click="increment"
        >+</span
      >
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { betterTextColor } from '@/vuejs/services/utils'

const emit = defineEmits(['updateQuantity'])
const props = defineProps({
  quantity: {
    type: Number,
    required: true,
  },
})

const qte = ref<number>(props.quantity)

const onInput = (event: InputEvent): void => {
  const inputValue = event.target.value
  const onlyNumbers = inputValue.replace(/[^0-9]/g, '') // Filtrer uniquement les chiffres

  if (onlyNumbers === 0 && onlyNumbers !== '') {
    qte.value = 1
  } else if (onlyNumbers > 99) {
    qte.value = 99
  } else {
    qte.value = parseInt(onlyNumbers)
  }
}

const onBlur = (): void => {
  if (qte.value !== props.quantity && qte.value >= 1 && qte.value <= 99) {
    emit('updateQuantity', {
      quantity: qte.value,
    })
  } else {
    qte.value = props.quantity
  }
}

const decrement = (): void => {
  if (qte.value > 1) {
    qte.value--
    emit('updateQuantity', {
      quantity: qte.value,
    })
  }
}

const increment = (): void => {
  if (qte.value < 99) {
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
  @apply float-left flex h-[30px] w-[24px] cursor-pointer items-center justify-center border border-x border-secondary bg-secondary  text-center text-base text-white md:text-lg lg:text-xl;
  -webkit-transition: all 0.3s;
  -o-transition: all 0.3s;
  transition: all 0.3s;
}

.skin-7 .num-in input {
  float: left;
  width: 35px;
  line-height: 34px;
  text-align: center;
}
</style>
