<template>
  <div>
    <div
      v-if="currentStatus.status === status.pending"
      class="border border-orange-500 text-orange-500 p-2 flex justify-center lg:w-2/3"
    >
      Votre rattachement est en cours
      <PendingIconComponent class="text-orange-700 w-5 stroke-gray-500 fill-orange-400 ml-1"/>
    </div>
    <div
      v-else
      class="border border-green-qantis text-green-qantis p-2 flex justify-around lg:w-2/3"
    >
      Vous bénéficier des conditions
      <CheckIconComponent class="stroke-green-qantis"/>
    </div>
    <div class="condition-beneficiaire mt-4">
      <p v-html="text" />
    </div>

    <div
      class="mt-6 flex flex-col justify-center md:flex-row md:justify-between"
    >
      <ButtonComponent
        v-if="btnContact"
        class="button-secondary md:mr-5"
        :class="{
          'md:w-1/2': btnLink.url,
        }"
      >
        <span>
          {{ btnContact }}
        </span>
      </ButtonComponent>
      <ButtonComponent
        v-if="btnLink.url"
        class="button-secondary md:mr-5"
        :class="{
          'md:w-1/2': btnContact,
        }"
        @click="openInNewTab(btnLink.url)"
      >
        {{ btnLink.name }}
      </ButtonComponent>
    </div>
  </div>
</template>
<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import PhoneLightIconComponent from '@/vuejs/modules/shared/icon/PhoneLightIconComponent.vue'
import { openInNewTab } from '@/vuejs/services/utils'
import { status } from '@/vuejs/modules/products'
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'
import { PropType } from 'vue'
import { AccountAccordCadre } from '@/vuejs/types/AccountAccordCadre'
import PendingIconComponent from '@/vuejs/modules/shared/icon/PendingIconComponent.vue'


const props = defineProps({
  currentStatus: {
    type: Object as PropType<AccountAccordCadre>,
    required: true
  },
  text: {
    type: String,
    default: null
  },
  btnContact: {
    type: String,
    default: null
  },
  btnLink: {
    type: Object,
    default: null
  },

})


</script>

<style scoped></style>
