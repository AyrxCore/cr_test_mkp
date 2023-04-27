<template>
  <NotificationGroup group="notif" position="bottom">
    <div
      class="pointer-events-none fixed inset-x-0 bottom-0 z-50 flex items-start justify-end p-6"
    >
      <div class="w-full max-w-sm">
        <Notification
          v-slot="{ notifications }"
          enter="transform ease-out duration-300 transition"
          enter-from="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
          enter-to="translate-y-0 opacity-100 sm:translate-x-0"
          leave="transition ease-in duration-500"
          leave-from="opacity-100"
          leave-to="opacity-0"
          move="transition duration-500"
          move-delay="delay-300"
        >
          <div
            v-for="notification in notifications"
            :key="notification.id"
            class="pointer-events-auto"
          >
            <div
              v-if="notification.type === 'info'"
              class="mx-auto mt-4 flex w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-md"
            >
              <div class="flex w-12 items-center justify-center bg-blue-500">
                <InformationIconComponent class="fill-white text-white" />
              </div>

              <div class="-mx-3 px-4 py-2">
                <div class="mx-3">
                  <span class="font-semibold text-blue-500">
                    {{ notification.title }}
                  </span>
                  <p class="text-sm text-gray-600">{{ notification.text }}</p>
                </div>
              </div>
            </div>
            <div
              v-if="notification.type === 'success'"
              class="mx-auto mt-4 flex w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-md"
            >
              <div
                class="flex w-12 items-center justify-center bg-green-qantis"
              >
                <CheckCircleIconComponent class="fill-white text-white" />
              </div>

              <div class="-mx-3 px-4 py-2">
                <div class="mx-3">
                  <span class="font-semibold text-green-qantis">
                    {{ notification.title }}
                  </span>
                  <p class="text-sm text-gray-600">{{ notification.text }}</p>
                </div>
              </div>
            </div>
            <div
              v-if="notification.type === 'error'"
              class="mx-auto mt-4 flex w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-md"
            >
              <div class="flex w-12 items-center justify-center bg-red-500">
                <WarningIconComponent class="fill-white" />
              </div>

              <div class="-mx-3 px-4 py-2">
                <div class="mx-3">
                  <span class="font-semibold text-red-500">
                    {{ notification.title }}
                  </span>
                  <p class="text-sm text-gray-600">{{ notification.text }}</p>
                </div>
              </div>
            </div>
          </div>
        </Notification>
      </div>
    </div>
  </NotificationGroup>
</template>

<script lang="ts" setup>
import { Notification, NotificationGroup } from 'notiwind'

import InformationIconComponent from '@/vuejs/modules/shared/icon/InformationIconComponent.vue'
import WarningIconComponent from '@/vuejs/modules/shared/icon/WarningIconComponent.vue'
import CheckIconComponent from '@/vuejs/modules/shared/icon/CheckIconComponent.vue'
import CheckCircleIconComponent from '@/vuejs/modules/shared/icon/CheckCircleIconComponent.vue'
</script>
