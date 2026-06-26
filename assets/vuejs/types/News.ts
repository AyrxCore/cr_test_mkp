// Types génériques pour les actualités (agnostiques du CMS)

export interface MediaAsset {
  filename: string
  alt?: string
  title?: string
}

export interface News {
  firstPublishedAt?: string
  slug: string
  fullSlug?: string
  categoryName?: string
  categoryColor?: string
  articleTitle?: string
  articleContent?: string
  articleImgMobile?: MediaAsset
  articleImgDesktop?: MediaAsset
  bannerImgMobile?: MediaAsset
  bannerImgDesktop?: MediaAsset
  ctaTxt?: string
  ctaLink?: string
  displayBanner?: boolean
}

export interface NewsResponse {
  status: string
  count: number
  data: News[]
}
