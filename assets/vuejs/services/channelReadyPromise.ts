/**
 * Singleton promise that resolves once the channel initialization attempt completes
 * (success or failure). Request interceptors await this to avoid sending requests
 * before the X-Channel header is available (MARKETPLACE-8C sentry race condition).
 *
 * The promise resolves regardless of whether the channel loaded successfully.
 * Interceptors must check channelCode after awaiting to handle the failure case.
 */
let resolveReady: () => void

export const channelReadyPromise = new Promise<void>((resolve) => {
  resolveReady = resolve
})

export const resolveChannelReady = (): void => resolveReady()
