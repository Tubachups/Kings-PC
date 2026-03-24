// resources/js/composables/usePsgc.ts
import { onMounted, ref } from 'vue';

type PsgcItem = {
  code: string;
  name: string;
};

type SetFieldValue = (field: string, value: string, shouldValidate?: boolean) => void;
type InitialAddressValues = Partial<Record<'region' | 'province' | 'city' | 'barangay', string | null | undefined>>;

const API = 'https://psgc.gitlab.io/api';

async function fetchJson<T>(url: string): Promise<T> {
  const res = await fetch(url);
  if (!res.ok) throw new Error(`PSGC request failed: ${res.status}`);
  return res.json() as Promise<T>;
}

export function usePsgc(setFieldValue?: SetFieldValue, initialValues?: InitialAddressValues) {
  const regions = ref<PsgcItem[]>([]);
  const provinces = ref<PsgcItem[]>([]);
  const cities = ref<PsgcItem[]>([]);
  const barangays = ref<PsgcItem[]>([]);

  const selectedRegionCode = ref('');
  const selectedProvinceCode = ref('');
  const selectedCityCode = ref('');
  const selectedBarangayCode = ref('');

  const isRegionWithoutProvinces = ref(false);
  const isLoadingRegions = ref(false);

  const setField = (field: string, value: string, shouldValidate = false) => {
    setFieldValue?.(field, value, shouldValidate);
  };

  const normalizeValue = (value?: string | null): string => value?.trim().toLowerCase() ?? '';

  const resolveItemCode = (items: PsgcItem[], value?: string | null): string => {
    const normalizedValue = normalizeValue(value);

    if (!normalizedValue) {
      return '';
    }

    return items.find((item) => {
      return normalizeValue(item.code) === normalizedValue || normalizeValue(item.name) === normalizedValue;
    })?.code ?? '';
  };

  const resetProvinceDown = () => {
    selectedProvinceCode.value = '';
    selectedCityCode.value = '';
    selectedBarangayCode.value = '';
    provinces.value = [];
    cities.value = [];
    barangays.value = [];
    setField('province', '', false);
    setField('city', '', false);
    setField('barangay', '', false);
  };

  const resetCityDown = () => {
    selectedCityCode.value = '';
    selectedBarangayCode.value = '';
    cities.value = [];
    barangays.value = [];
    setField('city', '', false);
    setField('barangay', '', false);
  };

  const resetBarangay = () => {
    selectedBarangayCode.value = '';
    barangays.value = [];
    setField('barangay', '', false);
  };

  const loadRegions = async () => {
    isLoadingRegions.value = true;
    try {
      regions.value = await fetchJson<PsgcItem[]>(`${API}/regions/`);
    } catch (error) {
      regions.value = [];
      console.error('Failed to load regions from PSGC API.', error);
    } finally {
      isLoadingRegions.value = false;
    }
  };

  const loadRegionChildren = async (regionCode: string): Promise<void> => {
    try {
      const data = await fetchJson<PsgcItem[]>(`${API}/regions/${regionCode}/provinces/`);

      if (!data.length) {
        isRegionWithoutProvinces.value = true;
        provinces.value = [];
        cities.value = await fetchJson<PsgcItem[]>(
          `${API}/regions/${regionCode}/cities-municipalities/`,
        );
        return;
      }

      isRegionWithoutProvinces.value = false;
      provinces.value = data;
      cities.value = [];
    } catch (error) {
      isRegionWithoutProvinces.value = false;
      provinces.value = [];
      cities.value = [];
      barangays.value = [];
      console.error('Failed to load region children from PSGC API.', error);
    }
  };

  const loadCitiesForProvince = async (provinceCode: string): Promise<void> => {
    try {
      cities.value = await fetchJson<PsgcItem[]>(
        `${API}/provinces/${provinceCode}/cities-municipalities/`,
      );
    } catch (error) {
      cities.value = [];
      barangays.value = [];
      console.error('Failed to load cities from PSGC API.', error);
    }
  };

  const loadBarangaysForCity = async (cityCode: string): Promise<void> => {
    try {
      barangays.value = await fetchJson<PsgcItem[]>(
        `${API}/cities-municipalities/${cityCode}/barangays/`,
      );
    } catch (error) {
      barangays.value = [];
      console.error('Failed to load barangays from PSGC API.', error);
    }
  };

  const hydrateSelections = async (valuesToHydrate: InitialAddressValues = initialValues ?? {}): Promise<void> => {
    await loadRegions();

    const regionCode = resolveItemCode(regions.value, valuesToHydrate.region);

    if (!regionCode) {
      return;
    }

    selectedRegionCode.value = regionCode;
    await loadRegionChildren(regionCode);

    if (!isRegionWithoutProvinces.value) {
      const provinceCode = resolveItemCode(provinces.value, valuesToHydrate.province);

      if (!provinceCode) {
        return;
      }

      selectedProvinceCode.value = provinceCode;
      await loadCitiesForProvince(provinceCode);
    }

    const cityCode = resolveItemCode(cities.value, valuesToHydrate.city);

    if (!cityCode) {
      return;
    }

    selectedCityCode.value = cityCode;
    await loadBarangaysForCity(cityCode);

    const barangayCode = resolveItemCode(barangays.value, valuesToHydrate.barangay);

    if (barangayCode) {
      selectedBarangayCode.value = barangayCode;
    }
  };

  const onRegionChange = async (regionCode: string) => {
    if (!regionCode) {
      return;
    }

    if (regionCode === selectedRegionCode.value) {
      const currentRegionName = regions.value.find(r => r.code === regionCode)?.name ?? regionCode;
      setField('region', currentRegionName, true);
      return;
    }

    selectedRegionCode.value = regionCode;
    const name = regions.value.find(r => r.code === regionCode)?.name ?? '';
    setField('region', name, true);

    resetProvinceDown();

    await loadRegionChildren(regionCode);
  };

  const onProvinceChange = async (provinceCode: string) => {
    if (!provinceCode) {
      return;
    }

    if (provinceCode === selectedProvinceCode.value) {
      const currentProvinceName = provinces.value.find(p => p.code === provinceCode)?.name ?? provinceCode;
      setField('province', currentProvinceName, true);
      return;
    }

    selectedProvinceCode.value = provinceCode;
    const name = provinces.value.find(p => p.code === provinceCode)?.name ?? '';
    setField('province', name, true);

    resetCityDown();

    await loadCitiesForProvince(provinceCode);
  };

  const onCityChange = async (cityCode: string) => {
    if (!cityCode) {
      return;
    }

    if (cityCode === selectedCityCode.value) {
      const currentCityName = cities.value.find(c => c.code === cityCode)?.name ?? cityCode;
      setField('city', currentCityName, true);
      return;
    }

    selectedCityCode.value = cityCode;
    const name = cities.value.find(c => c.code === cityCode)?.name ?? '';
    setField('city', name, true);

    resetBarangay();

    await loadBarangaysForCity(cityCode);
  };

  const onBarangayChange = (barangayCode: string) => {
    selectedBarangayCode.value = barangayCode;
    const name = barangays.value.find(b => b.code === barangayCode)?.name ?? '';
    setField('barangay', name, true);
  };

  onMounted(() => {
    void hydrateSelections();
  });

  return {
    regions,
    provinces,
    cities,
    barangays,
    selectedRegionCode,
    selectedProvinceCode,
    selectedCityCode,
    selectedBarangayCode,
    isRegionWithoutProvinces,
    isLoadingRegions,
    loadRegions,
    hydrateSelections,
    onRegionChange,
    onProvinceChange,
    onCityChange,
    onBarangayChange,
  };
}
