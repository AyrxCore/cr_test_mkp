import { useChannelStore } from '@/vuejs/stores/channel'

export function formatUrlWithChannelCode(url: string): string {
  const channelStore = useChannelStore()
  return url.indexOf('{{CHANNEL_CODE}}') !== -1
    ? url.replace('{{CHANNEL_CODE}}', channelStore.currentChannel.code)
    : url
}

export function getIdFromIri(iri: string | undefined) {
  const parts = iri?.split('/')
  return parts && parts.length > 1 ? parts[parts.length - 1] : null
}
