// resources/js/composables/usePsgc.ts
import { onMounted, ref } from 'vue';

type PsgcItem = {
  code: string;
  name: string;
};

type SetFieldValue = (field: string, value: string) => void;

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

  const setField = (field: string, value: string) => setFieldValue?.(field, value);

  const resetProvinceDown = () => {
    selectedProvinceCode.value = '';
    selectedCityCode.value = '';
    selectedBarangayCode.value = '';
    provinces.value = [];
    cities.value = [];
    barangays.value = [];
    setField('province', '');
    setField('city', '');
    setField('barangay', '');
  };

  const resetCityDown = () => {
    selectedCityCode.value = '';
    selectedBarangayCode.value = '';
    cities.value = [];
    barangays.value = [];
    setField('city', '');
    setField('barangay', '');
  };

  const resetBarangay = () => {
    selectedBarangayCode.value = '';
    barangays.value = [];
    setField('barangay', '');
  };

  const loadRegions = async () => {
    isLoadingRegions.value = true;
    try {
      regions.value = await fetchJson<PsgcItem[]>(`${API}/regions/`);
    } finally {
      isLoadingRegions.value = false;
    }
  };

  const onRegionChange = async (regionCode: string) => {
    selectedRegionCode.value = regionCode;
    const name = regions.value.find(r => r.code === regionCode)?.name ?? '';
    setField('region', name);

    resetProvinceDown();

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
  };

  const onProvinceChange = async (provinceCode: string) => {
    selectedProvinceCode.value = provinceCode;
    const name = provinces.value.find(p => p.code === provinceCode)?.name ?? '';
    setField('province', name);

    resetCityDown();

    cities.value = await fetchJson<PsgcItem[]>(
      `${API}/provinces/${provinceCode}/cities-municipalities/`,
    );
  };

  const onCityChange = async (cityCode: string) => {
    selectedCityCode.value = cityCode;
    const name = cities.value.find(c => c.code === cityCode)?.name ?? '';
    setField('city', name);

    resetBarangay();

    barangays.value = await fetchJson<PsgcItem[]>(
      `${API}/cities-municipalities/${cityCode}/barangays/`,
    );
  };

  const onBarangayChange = (barangayCode: string) => {
    selectedBarangayCode.value = barangayCode;
    const name = barangays.value.find(b => b.code === barangayCode)?.name ?? '';
    setField('barangay', name);
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
