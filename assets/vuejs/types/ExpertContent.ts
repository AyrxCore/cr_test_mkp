export interface ExpertContent {
  id: number
  slug: string
  date: Date

  mise_en_avant_homepage_img_desktop: string
  mise_en_avant_homepage_img_mobile: string
  page_actus_img_desktop: string
  page_actus_img_mobile: string
  slider_img_desktop: string
  slider_img_mobile: string
  article_img_desktop: string
  article_img_mobile: string

  articleTitle: string
  articleTeaser: string
  articleContent: string
  ctaTxt: string
  ctaLink: string
  categoryName: string
  categoryColor: string
}

export interface ExpertContentCategory {
  id: number
  name: string
  color: string
}
