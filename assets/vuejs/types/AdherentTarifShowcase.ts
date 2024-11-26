import { Adherent } from './Account'

export interface AdherentTarifShowcase {
  id: string | null
  adherent: Adherent | null
  accordId: string | null
  tarifId: string | null
  contactRequested: boolean
}
