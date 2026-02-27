<script setup lang="ts">
import { useFormContext } from 'vee-validate'
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { usePsgc } from '@/composables/usePsgc'

// Grab the setFieldValue function from the parent's form context
const { setFieldValue } = useFormContext()

const {
  regions, provinces, cities, barangays,
  selectedRegionCode, selectedProvinceCode, selectedCityCode, selectedBarangayCode,
  isRegionWithoutProvinces,
  onRegionChange, onProvinceChange, onCityChange, onBarangayChange,
} = usePsgc((field, value) => {
  setFieldValue(field, value)
})
</script>

<template>
  <div class="col-span-4">
    <FormField v-slot="{ componentField }" name="full_name">
      <FormItem><FormLabel>Full Name</FormLabel><FormControl><Input v-bind="componentField" /></FormControl><FormMessage /></FormItem>
    </FormField>
  </div>

  <div class="col-span-2">
    <FormField name="region">
      <FormItem>
        <FormLabel>Region</FormLabel>
        <Select :model-value="selectedRegionCode" @update:model-value="(val) => { if (typeof val === 'string') onRegionChange(val) }">
          <FormControl><SelectTrigger><SelectValue placeholder="Select Region" /></SelectTrigger></FormControl>
          <SelectContent>
            <SelectItem v-for="r in regions" :key="r.code" :value="r.code">{{ r.name }}</SelectItem>
          </SelectContent>
        </Select>
        <FormMessage />
      </FormItem>
    </FormField>
  </div>

  <div class="col-span-2">
    <FormField name="province">
      <FormItem>
        <FormLabel>Province</FormLabel>
        <Select
          :model-value="selectedProvinceCode"
          :disabled="isRegionWithoutProvinces || !provinces.length"
          @update:model-value="(val) => { if (typeof val === 'string') onProvinceChange(val) }"
        >
          <FormControl><SelectTrigger><SelectValue :placeholder="isRegionWithoutProvinces ? 'N/A' : 'Select Province'" /></SelectTrigger></FormControl>
          <SelectContent>
            <SelectItem v-for="p in provinces" :key="p.code" :value="p.code">{{ p.name }}</SelectItem>
          </SelectContent>
        </Select>
      </FormItem>
    </FormField>
  </div>

  <div class="col-span-2">
    <FormField name="city">
      <FormItem>
        <FormLabel>City</FormLabel>
        <Select
          :model-value="selectedCityCode"
          :disabled="!cities.length"
          @update:model-value="(val) => { if (typeof val === 'string') onCityChange(val) }"
        >
          <FormControl><SelectTrigger><SelectValue placeholder="Select City" /></SelectTrigger></FormControl>
          <SelectContent>
            <SelectItem v-for="c in cities" :key="c.code" :value="c.code">{{ c.name }}</SelectItem>
          </SelectContent>
        </Select>
        <FormMessage />
      </FormItem>
    </FormField>
  </div>

  <div class="col-span-2">
    <FormField name="barangay">
      <FormItem>
        <FormLabel>Barangay</FormLabel>
        <Select
          :model-value="selectedBarangayCode"
          :disabled="!barangays.length"
          @update:model-value="(val) => { if (typeof val === 'string') onBarangayChange(val) }"
        >
          <FormControl><SelectTrigger><SelectValue placeholder="Select Barangay" /></SelectTrigger></FormControl>
          <SelectContent>
            <SelectItem v-for="b in barangays" :key="b.code" :value="b.code">{{ b.name }}</SelectItem>
          </SelectContent>
        </Select>
        <FormMessage />
      </FormItem>
    </FormField>
  </div>

  <div class="col-span-4">
    <FormField v-slot="{ componentField }" name="address">
      <FormItem><FormLabel>Street Address</FormLabel><FormControl><Input placeholder="House No / Street / Landmark" v-bind="componentField" /></FormControl><FormMessage /></FormItem>
    </FormField>
  </div>
</template>
