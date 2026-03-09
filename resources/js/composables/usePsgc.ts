// resources/js/composables/usePsgc.ts
import { onMounted, ref } from 'vue';

type PsgcItem = {
  code: string;
  name: string;
};

type SetFieldValue = (field: string, value: string, shouldValidate?: boolean) => void;

const API = 'https://psgc.gitlab.io/api';

async function fetchJson<T>(url: string): Promise<T> {
  const res = await fetch(url);
  if (!res.ok) throw new Error(`PSGC request failed: ${res.status}`);
  return res.json() as Promise<T>;
}

export function usePsgc(setFieldValue?: SetFieldValue) {
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

    try {
      const data = await fetchJson<PsgcItem[]>(`${API}/regions/${regionCode}/provinces/`);
      if (!data.length) {
        isRegionWithoutProvinces.value = true;
        cities.value = await fetchJson<PsgcItem[]>(
          `${API}/regions/${regionCode}/cities-municipalities/`,
        );
      } else {
        isRegionWithoutProvinces.value = false;
        provinces.value = data;
      }
    } catch (error) {
      isRegionWithoutProvinces.value = false;
      provinces.value = [];
      cities.value = [];
      barangays.value = [];
      console.error('Failed to load region children from PSGC API.', error);
    }
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

    try {
      barangays.value = await fetchJson<PsgcItem[]>(
        `${API}/cities-municipalities/${cityCode}/barangays/`,
      );
    } catch (error) {
      barangays.value = [];
      console.error('Failed to load barangays from PSGC API.', error);
    }
  };

  const onBarangayChange = (barangayCode: string) => {
    selectedBarangayCode.value = barangayCode;
    const name = barangays.value.find(b => b.code === barangayCode)?.name ?? '';
    setField('barangay', name, true);
  };

  onMounted(loadRegions);

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
    onRegionChange,
    onProvinceChange,
    onCityChange,
    onBarangayChange,
  };
}
