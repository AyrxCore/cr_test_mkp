import { defineStore } from 'pinia'
import {
  Channel,
  ChannelStoreState,
  ChannelDocuments,
} from '@/vuejs/types/Channel'
import { notifyError } from '@/vuejs/services/utils'
import ChannelHttpClient from '@/vuejs/services/httpclient/ChannelHttpClient'
import { useCommonStore } from '@/vuejs/stores/common'
import { parsePhoneNumber } from 'libphonenumber-js'
import {
  HOME_ACCORD_CADRE_PROPERTY,
  HOME_PRODUCTS_SELECTION_PROPERTY,
} from '@/vuejs/services/const'

const commonStore = useCommonStore()

export const useChannelStore = defineStore({
  id: 'channel',
  state: (): ChannelStoreState => ({
    currentChannel: null,
  }),

  actions: {
    async getChannel(hostname: string): Promise<void> {
      try {
        const channel: Channel =
          await ChannelHttpClient.get().getChannelByHost(hostname)

        commonStore.setChannelCode(channel.code)

        this.currentChannel = {
          id: channel.id,
          code: channel.code,
          email: channel.email,
          hostname: channel.hostname,
          name: channel.name,
          phoneNumber: channel.phoneNumber,
          whiteLabel: channel.whiteLabel,
          design: {
            primaryColor: channel.design.primaryColor,
            secondaryColor: channel.design.secondaryColor,
            textColor: channel.design.textColor,
            logo: channel.design.logo,
            favicon: channel.design.favicon,
          },
          documents: {
            privacyPolicy: channel.documents.privacyPolicy,
            legalTerms: channel.documents.legalTerms,
            generalTermsOfUse: channel.documents.generalTermsOfUse,
          },
          options: channel.options,
        }
      } catch (error) {
        this.currentChannel = null

        notifyError(
          'Une erreur est survenue, merci de contacter un administrateur.',
        )
      }
    },
    isAllowedToShow(block: string | undefined) {
      return (
        block !== undefined &&
        block in this.currentChannel.options &&
        this.currentChannel.options[block] === 'true'
      )
    },
  },

  getters: {
    channel(): Channel | null {
      return this.currentChannel
    },
    isWhiteLabel(): string | null {
      return this.currentChannel?.whiteLabel
    },
    channelPrimaryColor(): string | null {
      return this.currentChannel?.design.primaryColor
    },
    channelSecondaryColor(): string | null {
      return this.currentChannel?.design.secondaryColor
    },
    channelTextColor(): string | null {
      return this.currentChannel?.design.textColor
    },
    formattedPhoneNumber(): string {
      return (
        this.currentChannel?.phoneNumber &&
        parsePhoneNumber(this.currentChannel.phoneNumber).formatNational()
      )
    },
    channelDocuments(): ChannelDocuments | null {
      return this.currentChannel?.documents
    },
    channelLegalTermsLink(): string | null {
      return this.channelDocuments?.legalTerms?.startsWith('https')
        ? this.channelDocuments?.legalTerms
        : '/mentions-legales'
    },
    channelGeneralTermsOfUseLink(): string | null {
      return this.channelDocuments?.generalTermsOfUse?.startsWith('https')
        ? this.channelDocuments?.generalTermsOfUse
        : '/conditions-generales-d-utilisation'
    },
    channelPrivacyPolicyLink(): string | null {
      return this.channelDocuments?.privacyPolicy?.startsWith('https')
        ? this.channelDocuments?.privacyPolicy
        : '/politique-de-confidentialite'
    },
    channelSliderAccordsCadresProperty(): Object {
      let accordCadreProperty = HOME_ACCORD_CADRE_PROPERTY
      if (
        this.channel.options['HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID']
      ) {
        let copyAccordCadreProperty = Object.assign(
          {},
          HOME_ACCORD_CADRE_PROPERTY,
        )
        copyAccordCadreProperty.properties.value = parseInt(
          this.channel.options[
            'HOMEPAGE_ACCORD_CADRE_CHANNEL_PROPERTY_VALUE_ID'
          ],
        )
        accordCadreProperty = copyAccordCadreProperty
      }
      return accordCadreProperty
    },
    channelSliderProductsSelectionProperty(): Object {
      let productSelectionProperty = HOME_PRODUCTS_SELECTION_PROPERTY
      if (
        this.channel.options[
          'HOMEPAGE_PRODUCTS_SELECTION_CHANNEL_PROPERTY_VALUE_ID'
        ]
      ) {
        let copySelectionProperty = Object.assign(
          {},
          HOME_PRODUCTS_SELECTION_PROPERTY,
        )
        copySelectionProperty.properties.value = parseInt(
          this.channel.options[
            'HOMEPAGE_PRODUCTS_SELECTION_CHANNEL_PROPERTY_VALUE_ID'
          ],
        )
        productSelectionProperty = copySelectionProperty
      }
      return productSelectionProperty
    },
  },
})
