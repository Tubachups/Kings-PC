import {
    builder as builderRoute,
    builds as buildsRoute,
    components as componentsRoute,
    contacts as contactsRoute,
    about as aboutRoute,
} from '@/routes';

export const PART_CATEGORIES = [
    { cat: 'gpu',     keywords: ['rtx', 'rx 9', 'rx9', 'radeon rx', 'gtx', 'gddr'] },
    { cat: 'cpu',     keywords: ['ryzen 5', 'ryzen 7', 'ryzen 9', 'r5 5', 'r7 5', 'r9 7', 'intel core', '9700x', '8700f', '7800x3d', '7700 ', '7500f', '5600 ', '5700x', '7700x'] },
    { cat: 'cooler',  keywords: ['x120', 'x240', 'x360', ' aio', 'warframe', 'astroshel', 'tower cooler', 'cpu cooler', 'refined se', 'assasin', 'assassin', 'peerless', 'e400plus'] },
    { cat: 'mobo',    keywords: ['b550', 'b650', 'b850', 'x670', 'z790', 'z890', 'battleax b', 'asrock b', 'gigabyte b', 'msi b', 'colorful b'] },
    { cat: 'ram',     keywords: ['mhz', 'tforce', 'apacer', 'adata xpg', 'patriot viper', 'hiksemi', 'sharpblade', '2x8gb', '2x16gb', '4x8gb', 'vengeance rgb'] },
    { cat: 'storage', keywords: ['nvme', ' ssd', 'spatium', 'samsung 9', 'adata legend', 'lexar nm', 'biwin nv', 'biwin x5', 'viper lite', 'kingston nv', 'kingston kc', '1tb ', '2tb ', '500gb '] },
    { cat: 'psu',     keywords: ['650w', '750w', '850w', '1000w', 'corsair cx', 'cougar gr', 'msi mag a', 'gigabyte p6', 'cooler master mwe', '1stplayer', '80+ gold', '80+ silver', '80+ bronze', 'full modular', 'fullmodular', 'steampunk'] },
    { cat: 'case',    keywords: ['case', 'midtower', 'df db', 'df a', 'df m', 'deepcool ch', 'okinos', 'coolman reyna', 'coolman spectra', 'ds950', 'reyna dual', 'darkflash', 'tecware', 'v300 chamber', 'millenium', 'mirage4', 'timber m', 'ch160'] },
    { cat: 'fans',    keywords: ['coolman drift', 'cyber argb', 'argb fans', 'aRGB fans', 'jungle leopard', 'non led fan', 'drift fury'] },
    { cat: 'extra',   keywords: ['monitor', '"', 'lcd screen', 'cable cover', 'flexible', 'keyboard'] },
]

export const SORT_OPTIONS = [
    { value: 'newest', label: 'Date Posted (Newest)' },
    { value: 'oldest', label: 'Date Posted (Oldest)' },
    { value: 'likes',  label: 'Highest Likes' },
    { value: 'price',  label: 'Highest Price' },
    { value: 'price_low', label: 'Lowest Price'}
]

export const steps = [
  { step: 1, title: 'Shipping', description: 'Address info' },
  { step: 2, title: 'Review', description: 'Check items' },
  { step: 3, title: 'Payment', description: 'Pay method' },
]


export const usefulLinks = [
    { label: 'Career' },
    { label: 'Help' },
    { label: 'Works' },
    { label: 'News' },
    { label: 'Partners' },
    { label: 'Community' },
    { label: 'Support' },
];

export const pages = [
    { label: 'Builder', route: builderRoute() },
    { label: 'Products', route: componentsRoute() },
    { label: 'Completed Builds', route: buildsRoute() },
    { label: 'Contact Us', route: contactsRoute() },
    { label: 'About', route: aboutRoute() },
];

export const aboutStats = [
    { label: 'Custom builds delivered', value: '1,000+' },
    { label: 'Average build time', value: '5-7 days' },
    { label: 'Customer satisfaction', value: '100%' },
    { label: 'Serving customers', value: '7+ years' },
];

export const aboutHighlights = [
    {
        title: 'Consult-first builds',
        description: 'We align budgets, performance goals, and aesthetic preferences before recommending parts.',
    },
    {
        title: 'Trusted sourcing',
        description: 'We work with reliable distributors and validate every component before assembly.',
    },
    {
        title: 'Post-build support',
        description: 'We help with upgrades, troubleshooting, and tuning long after your system ships.',
    },
];

export const aboutValues = [
    {
        title: 'Performance with purpose',
        description: 'Every component is selected to balance speed, stability, and power efficiency.',
    },
    {
        title: 'Clean, documented builds',
        description: 'We share build notes, benchmarks, and cable management details for transparency.',
    },
    {
        title: 'People-first service',
        description: 'We answer questions quickly and keep communication clear from start to finish.',
    },


];

export const categoryOrder = [
    'motherboard',
    'cpu',
    'ram',
    'storages',
    'psu',
    'gpu',
    'case',
    'cooling',
]
