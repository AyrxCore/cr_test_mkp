import { notify } from 'notiwind'

import { Address } from '../types/Address'
import imgDefault from '@/vuejs/assets/img/default-image.png'
import { format } from 'date-fns'

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
  let params = new URLSearchParams(document.location.search)
  return params.get(name)
}

export function notifyError(text: string, time: number = 10000): void {
  notify(
    {
      group: 'notif',
      type: 'error',
      title: 'Une erreur est survenue',
      text: text,
    },
    time,
  )
}

export function notifySuccess(text: string, time: number = 10000): void {
  notify(
    {
      group: 'notif',
      type: 'success',
      title: 'Succès!',
      text: text,
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
