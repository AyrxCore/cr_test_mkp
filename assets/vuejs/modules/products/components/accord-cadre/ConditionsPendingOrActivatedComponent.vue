<template>
  <div>
    <div
      v-if="currentStatus.status === status.pending"
      class="flex justify-center border border-orange-500 p-2 text-orange-500 lg:w-2/3"
    >
      Votre rattachement est en cours
      <PendingIconComponent
        class="ml-1 w-5 fill-orange-400 stroke-gray-500 text-orange-700"
      />
    </div>
    <div
      v-else
      class="flex justify-around border border-green-qantis p-2 text-green-qantis lg:w-2/3"
    >
      Vous bénéficiez des conditions
      <CheckIconComponent class="stroke-green-qantis" />
    </div>
    <div class="condition-beneficiaire mt-4">
      <p v-html="text" />
    </div>

    <div
      class="mt-6 flex flex-col justify-center md:flex-row md:justify-between"
    >
      <ButtonComponent
        v-if="cta1.name && cta1.url"
        class="button-secondary focus:!bg-white focus:text-secondary md:mr-5"
        :class="{
          'md:w-1/2': cta2.name && (cta2.url || cta2.mailto),
        }"
        @click="openInNewTab(cta1.url)"
      >
        <span>
          {{ cta1.name }}
        </span>
      </ButtonComponent>
      <a
        v-else-if="cta1.name && cta1.mailto"
        class="button button-secondary md:mr-5"
        :class="{
          'md:w-1/2': cta2.name && (cta2.url || cta2.mailto),
        }"
        :href="cta1.mailto"
      >
        <span>
          {{ cta1.name }}
        </span>
      </a>
      <ButtonComponent
        v-if="cta2.name && cta2.url"
        class="button-secondary md:mr-5"
        :class="{
          'md:w-1/2': cta1.name && (cta1.url || cta1.mailto),
        }"
        @click="openInNewTab(cta2.url)"
      >
        {{ cta2.name }}
      </ButtonComponent>
      <a
        v-else-if="cta2.name && cta2.mailto"
        class="button button-secondary md:mr-5"
        :class="{
          'md:w-1/2': cta1.name && (cta1.url || cta1.mailto),
        }"
        :href="cta2.mailto"
      >
        <span>
          {{ cta2.name }}
        </span>
      </a>
    </div>
  </div>
</template>
<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { openInNewTab } from '@/vuejs/services/utils'
import { status } from '@/vuejs/modules/products'
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'
import { computed, PropType } from 'vue'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'
import PendingIconComponent from '@/vuejs/modules/shared/icon/PendingIconComponent.vue'

const props = defineProps({
  currentStatus: {
    type: Object as PropType<AccountAccordCadre>,
    required: true,
  },
  properties: {
    type: Object,
    default: null,
  },
})

const text = computed(() => {
  return props.currentStatus.status === status.value.pending
    ? props.properties.process_pending
    : props.properties.process_activated
})

const cta1 = computed(() => {
  if (props.currentStatus.status === status.value.pending) {
    return {
      name: props.properties.cta1_text_pending,
      url: props.properties.cta1_link_pending,
      mailto: props.properties.cta1_mailto_pending,
    }
  } else {
    return {
      name: props.properties.cta1_text_activated,
      url: props.properties.cta1_link_activated,
      mailto: props.properties.cta1_mailto_activated,
    }
  }
})

const cta2 = computed(() => {
  if (props.currentStatus.status === status.value.pending) {
    return {
      name: props.properties.cta2_text_pending,
      url: props.properties.cta2_link_pending,
      mailto: props.properties.cta2_mailto_pending,
    }
  } else {
    return {
      name: props.properties.cta2_text_activated,
      url: props.properties.cta2_link_activated,
      mailto: props.properties.cta2_mailto_activated,
    }
  }
})
</script>

<style scoped></style>
