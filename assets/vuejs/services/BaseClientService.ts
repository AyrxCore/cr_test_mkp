import axios, { AxiosInstance, AxiosResponse, AxiosError } from 'axios'

class BaseClientService {
  public apiClient: AxiosInstance
  public static self: InstanceType<any>

  public isPatch: boolean

  public constructor(isPatch: boolean) {
    this.isPatch = isPatch

    const headers = {
      accept: 'application/json',
    }

    headers['Content-Type'] = 'application/json'

    if (isPatch) {
      headers['Content-Type'] = 'application/merge-patch+json'
    }

    this.apiClient = axios.create({
      baseURL: '/api',
      withCredentials: true,
      headers,
    })

    this.apiClient.interceptors.response.use(
      (response: Promise<AxiosResponse> | AxiosResponse | undefined) => {
        return response
      },
      async (error: AxiosError) => {
        const originalConfig = error.config
        if (error.response?.status === 401 && !originalConfig._retry) {
          if (error.config.url !== 'authentication/token') {
            location.reload()
          } else {
            console.log(error.config.url)
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
