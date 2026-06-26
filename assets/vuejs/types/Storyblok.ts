export interface StoryblokAsset {
  id?: number
  alt?: string
  name?: string
  focus?: string
  title?: string
  source?: string
  filename: string
  copyright?: string
  fieldtype?: string
  meta_data?: unknown[]
  is_external_url?: boolean
}

export interface StoryblokActuContent {
  _uid?: string
  component: string
  articleTitle?: string
  categoryName?: string
  categoryColor?: string
  articleContent?: string
  article_img_mobile?: StoryblokAsset
  article_img_desktop?: StoryblokAsset
  banner_image_mobile?: StoryblokAsset
  banner_image_desktop?: StoryblokAsset
  ctaTxt?: string
  ctaLink?: string
  displayBanner?: boolean
  [key: string]: unknown
}

export interface StoryblokStory {
  name?: string
  created_at?: string
  published_at?: string
  updated_at?: string
  id?: number
  uuid?: string
  slug: string
  full_slug?: string
  content?: StoryblokActuContent
  first_published_at?: string
}

export interface NewsResponse {
  status: string
  count: number
  data: StoryblokStory[]
}
