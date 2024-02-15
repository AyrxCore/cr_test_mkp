import { useChannelStore } from '@/vuejs/stores/channel'

export function formatUrlWithChannelCode(url: string): string {
  const channelStore = useChannelStore()
  return url.indexOf('{{CHANNEL_CODE}}') !== -1
    ? url.replace('{{CHANNEL_CODE}}', channelStore.currentChannel.code)
    : url
}
