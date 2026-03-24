export type CheckoutPaymentMethod = 'gcash' | 'cod' | 'card' | ''

export type CheckoutDraft = {
  full_name: string
  address: string
  region: string
  province: string
  city: string
  barangay: string
  payment_method: CheckoutPaymentMethod
}

export type CheckoutPersistedState = {
  draft: CheckoutDraft
  stepIndex: number
}
