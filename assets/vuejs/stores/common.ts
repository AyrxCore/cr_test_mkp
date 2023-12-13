import { defineStore } from 'pinia'

export const useCommonStore = defineStore({
  id: 'common',
  state: () => ({
    channelCode: null as string | null,
  }),
  actions: {
    setChannelCode(code: string): void {
      this.channelCode = code
    },
  },
})
