export interface AddressSearchResult {
  place_id: number
  lat: string
  lon: string
  addresstype: string
  name: string
  display_name: string
  address: {
    house_number?: string
    road?: string
    postcode?: string
    village?: string
    city?: string
    town?: string
    state: string
    region: string
    country: string
    country_code: string
  }
}

export async function searchAddress(
  query: string,
  params: string[],
): Promise<AddressSearchResult[]> {
  const response = await fetch(
    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
      query,
    )}${params.length ? `&${params.join('&')}` : ''}`,
    {
      headers: {
        'Accept-Language': 'fr',
      },
    },
  )
  return await response.json()
}
