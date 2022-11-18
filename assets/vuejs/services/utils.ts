export function getImage(urlImage: string): string {
  return new URL(urlImage, import.meta.url).href
}
