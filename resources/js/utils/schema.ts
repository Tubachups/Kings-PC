import * as z from 'zod'

export const step1Schema = z.object({
  full_name: z.string().min(2, "Name is required"),
  address: z.string().min(5, "Address is required"),
  region: z.string().min(1, "Region is required"),
  province: z.string().optional().nullable().or(z.literal('')),
  city: z.string().min(1, "City is required"),
  barangay: z.string().min(1, "Barangay is required"),
})

export const step2Schema = z.object({}) 

export const step3Schema = z.object({
  payment_method: z.enum(['gcash', 'cod', 'card'], {
    required_error: "Please select a payment method",
  }),
})