import { StoreData } from '@/vuejs/types/Seller'
import { Product } from '@/vuejs/types/Product.ts'

export interface AccordApiResponse {
  id: string
  name: string
  logo: string
  stores: StoreData[]
}

export interface AccordCadreState {
  accordCadre: Product
  errorLoading: boolean | null
}

export interface AccordCadreContent {
  tarifId: string
  labelCtaRattachement: string
  urlCtaRattachement: string
  listBlocks: AccordCadreBlocks
  name: string
  type: string
  labelNotActivated: string
  labelPending: string
  labelActivated: string
  confirmationLayerDescription: string
  confirmationLayerSuccess: string
  contactForm: boolean
}

export interface AccordCadreBlocks {
  bannerBlock: BannerBlock
  presentationBlock: PresentationBlock
  negociatedTermsBlock: NegociatedTermsBlock
  stepsBlock: StepsBlock
}

export interface BannerBlock {
  logoUrl: string
  componentName: string
  badgeTextTop: string
  badgeTextBottom: string
  imgBannerUrlDesktop: string
  imgBannerUrlMobile: string
}

export interface PresentationBlock {
  title: string
  rseScore: string
  componentName: string
  description: string
  bulletpoints: string
  layerMoreInformationsDescription: string
  layerMoreInformationsPhone: string
  layerMoreInformationsPhoneDescription: string
  layerMoreInformationsAssetButtons: AssetButton[]
}

export interface NegociatedTermsBlock {
  componentName: string
  title: string
  description: string
  detailsTitle: string
  detailsContent: string
  negociatedTermsButtonLabel: string
  negociatedTermsLayerItems: ImageItem[]
  assetButtons: AssetButton[]
}

export interface StepsBlock {
  componentName: string
  title: string
  stepItems: StepsBlockItem[]
}

export interface StepsBlockItem {
  title: string
  description: string
}

export interface AssetButton {
  buttonLabel: string
  assetLink: string
}

export interface ImageItem {
  imgLink: string
}

export interface StepItem {
  title: string
  description: string
}
