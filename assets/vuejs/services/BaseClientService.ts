import axios, {
  AxiosError,
  AxiosInstance,
  AxiosRequestConfig,
  AxiosResponse,
} from 'axios'
import Cookies from 'js-cookie'
import { useCommonStore } from '@/vuejs/stores/common'

// Initialisation différée pour éviter l'erreur "no active Pinia"
let commonStore: ReturnType<typeof useCommonStore> | null = null

const getCommonStore = () => {
  if (!commonStore) {
    commonStore = useCommonStore()
  }
  return commonStore
}

class BaseClientService {
  public static self: InstanceType<any>
  public apiClient: AxiosInstance
  public isPatch: boolean

  public constructor(isPatch: boolean) {
    this.isPatch = isPatch
    const headers = {
      accept: 'application/json',
      'Content-Type': isPatch
        ? 'application/merge-patch+json'
        : 'application/json',
    }

    this.apiClient = axios.create({
      baseURL: '/api',
      withCredentials: true,
      headers,
    })

    this.apiClient.interceptors.request.use(
      (config: AxiosRequestConfig): AxiosRequestConfig => {
        if (!config.url.includes('/channels/by-host/')) {
          const store = getCommonStore()
          config.headers['X-channel'] = store.channelCode
        }

        return config
      },
      (error: AxiosError): Promise<AxiosError> => {
        return Promise.reject(error)
      },
    )

    this.apiClient.interceptors.response.use(
      (response: Promise<AxiosResponse> | AxiosResponse | undefined) => {
        return response
      },
      async (error: AxiosError) => {
        if (error.response?.status === 401) {
          if (error.config.url !== 'authentication/token') {
            Cookies.remove('BEARER')
            Cookies.remove('PHPSESSID')
            Cookies.remove('neoAutoLogin')
            location.reload()
          }
        }
        if (error.response?.status === 503) {
          if (error.response?.data?.maintenance) {
            location.reload()
          }
        }
        return Promise.reject(error)
      },
    )
  }

  public static get<T extends typeof BaseClientService>(
    this: T,
    isPatch = false,
  ): InstanceType<T> {
    if (this.self && this.self.ispatch === isPatch) {
      return this.self
    }

    const instance = new this(isPatch)
    this.self = instance
    return instance as InstanceType<T>
  }
}

export default BaseClientService
