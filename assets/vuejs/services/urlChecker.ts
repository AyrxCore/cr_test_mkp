export function isAbsoluteUrl(url: string) {
  const absoluteUrlRegex = /^(http:\/\/|https:\/\/)/i

  return absoluteUrlRegex.test(url)
}

export function isFilePath(url: string) {
  return /\.([0-9a-z]+)(?:[?#]|$)/i.exec(url)
}
