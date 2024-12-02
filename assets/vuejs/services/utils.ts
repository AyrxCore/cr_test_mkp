import { notify } from 'notiwind'
import Cookies from 'js-cookie'
import { useHead } from '@unhead/vue'

import { Address } from '../types/Address'
import imgDefault from '@/vuejs/assets/img/default-image.png'
import { format } from 'date-fns'
import { useChannelStore } from '@/vuejs/stores/channel'

export function getImage(urlImage: string): string {
  return new URL(urlImage, import.meta.url).href
}

export function openInNewTab(url) {
  window.open(url, '_blank', 'noreferrer')
}

export function getUpplerImage(path: string | null) {
  return path !== null ? path : getImage(imgDefault)
}

export function formatPrice(price: number): string {
  return price.toLocaleString('fr', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}

export function formatAddress(address: Address): string {
  if (!address) return null
  return `${!address.company ? '' : address.company + ', '} ${address.street} ${
    address.postcode
  } ${address.city}`
}

export function getUrlParam(name: string): string | null {
  const params = new URLSearchParams(document.location.search)
  return params.get(name)
}

export function notifyError(text: string, time: number = 10000): void {
  notify(
    {
      group: 'notif',
      type: 'error',
      title: 'Une erreur est survenue',
      text,
    },
    time,
  )
}

export function notifySuccess(text: string, time: number = 10000): void {
  notify(
    {
      group: 'notif',
      type: 'success',
      title: 'Succès !',
      text,
    },
    time,
  )
}

export function arrayEqual(arr1, arr2): boolean {
  if (arr1.length !== arr2.length) {
    return false
  }

  for (let i = 0; i < arr1.length; i++) {
    if (arr1[i] !== arr2[i]) {
      return false
    }
  }

  return true
}

export function isUrl(str) {
  try {
    return new URL(str)
  } catch (e) {
    return false
  }
}

export function formatDateFr(date: Date | null) {
  return date !== null ? format(new Date(date), 'dd/MM/yyyy') : null
}

export function hexToBinary(hexString) {
  const buffer = new Uint8Array(hexString.length / 2)
  for (let i = 0; i < hexString.length; i += 2) {
    buffer[i / 2] = parseInt(hexString.substr(i, 2), 16)
  }
  return buffer
}

function getColorBrightness(hexColor) {
  const r = parseInt(hexColor.slice(1, 3), 16)
  const g = parseInt(hexColor.slice(3, 5), 16)
  const b = parseInt(hexColor.slice(5, 7), 16)
  return Math.round((r * 299 + g * 587 + b * 114) / 1000)
}

export const betterTextColor = (bgColor: 'primary' | 'secondary') => {
  const channelStore = useChannelStore()
  const hexColor =
    bgColor === 'primary'
      ? channelStore.channelPrimaryColor
      : channelStore.channelSecondaryColor
  if (hexColor) {
    const brightness = getColorBrightness(hexColor)
    return brightness > 170 ? 'black' : 'white'
  } else {
    return 'black'
  }
}

export function getCookie(name: string | undefined): string | undefined {
  if (!name) {
    return null
  }

  return Cookies.get(name)
}

export function setHeadTitle(title: string): void {
  useHead({
    title: title,

    meta: [
      {
        property: 'og:title',
        content: title,
      },
    ],
  })
}
