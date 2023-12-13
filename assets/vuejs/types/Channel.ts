interface ChannelDesign {
  primaryColor: string | null
  secondaryColor: string | null
  textColor: string | null
  logo: string | null
  favicon: string | null
  banner: string | null
  bannerTitle: string | null
}

interface ChannelDocuments {
  privacyPolicy: string | null
  legalTerms: string | null
  generalTermsOfUse: string | null
}

export interface ChannelOptions {
  name: string | null
  value: string | null
}

export interface Channel {
  id: string | null
  code: string | null
  email: string | null
  hostname: string | null
  name: string | null
  phoneNumber: string | null
  whiteLabel: boolean | null
  design: ChannelDesign | null
  documents: ChannelDocuments | null
  options: ChannelOptions | null
}

export interface ChannelStoreState {
  currentChannel: Channel | null
}
