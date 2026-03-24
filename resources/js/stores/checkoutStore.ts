import { defineStore } from 'pinia'
import type { CheckoutDraft, CheckoutPaymentMethod, CheckoutPersistedState } from '@/types/checkout'

const STORAGE_KEY = 'checkout-draft'

const createEmptyDraft = (): CheckoutDraft => ({
  full_name: '',
  address: '',
  region: '',
  province: '',
  city: '',
  barangay: '',
  payment_method: '',
})

const clampStepIndex = (stepIndex: number): number => {
  if (!Number.isFinite(stepIndex)) {
    return 1
  }

  return Math.min(Math.max(Math.trunc(stepIndex), 1), 3)
}

const canUseSessionStorage = (): boolean => {
  return typeof window !== 'undefined' && typeof window.sessionStorage !== 'undefined'
}

const loadPersistedState = (): CheckoutPersistedState => {
  const fallbackState: CheckoutPersistedState = {
    draft: createEmptyDraft(),
    stepIndex: 1,
  }

  if (!canUseSessionStorage()) {
    return fallbackState
  }

  try {
    const rawValue = window.sessionStorage.getItem(STORAGE_KEY)

    if (!rawValue) {
      return fallbackState
    }

    const parsedValue = JSON.parse(rawValue) as Partial<CheckoutPersistedState>

    return {
      draft: {
        ...fallbackState.draft,
        ...(parsedValue.draft ?? {}),
      },
      stepIndex: clampStepIndex(parsedValue.stepIndex ?? 1),
    }
  } catch (error) {
    console.error('Failed to load checkout draft from session storage.', error)

    return fallbackState
  }
}

const persistState = (state: CheckoutPersistedState): void => {
  if (!canUseSessionStorage()) {
    return
  }

  try {
    window.sessionStorage.setItem(STORAGE_KEY, JSON.stringify(state))
  } catch (error) {
    console.error('Failed to persist checkout draft to session storage.', error)
  }
}

const clearPersistedState = (): void => {
  if (!canUseSessionStorage()) {
    return
  }

  window.sessionStorage.removeItem(STORAGE_KEY)
}

export const useCheckoutStore = defineStore('checkout', {
  state: (): CheckoutPersistedState => loadPersistedState(),

  getters: {
    initialValues: (state): CheckoutDraft => ({
      ...state.draft,
    }),
  },

  actions: {
    persist(): void {
      persistState({
        draft: { ...this.draft },
        stepIndex: this.stepIndex,
      })
    },

    setShippingDraft(payload: Partial<Omit<CheckoutDraft, 'payment_method'>>): void {
      this.draft = {
        ...this.draft,
        ...payload,
      }

      this.persist()
    },

    setPaymentMethod(paymentMethod: CheckoutPaymentMethod): void {
      this.draft.payment_method = paymentMethod
      this.persist()
    },

    setStepIndex(stepIndex: number): void {
      this.stepIndex = clampStepIndex(stepIndex)
      this.persist()
    },

    clearDraft(): void {
      this.draft = createEmptyDraft()
      this.stepIndex = 1
      clearPersistedState()
    },
  },
})
