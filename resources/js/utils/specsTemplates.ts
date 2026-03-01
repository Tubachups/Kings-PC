export const specsTemplates = {
    '1': {
        // Motherboard
        color: 'Black / Silver',
        memory_max: '128 GB',
        socket_cpu: 'AM4',
        form_factor: 'Micro ATX',
        memory_slots: '4',
    },
    '2': {
        // CPU
        tdp: '120 W',
        core_count: 8,
        microarchitecture: 'Zen 5',
        integrated_graphics: 'Radeon',
        performance_core_clock: '4.7 GHz',
        performance_core_boost_clock: '5.2 GHz',
    },
    '3': {
        // RAM
        color: 'Black',
        speed: 'DDR5-6000',
        modules: '2 x 16GB',
        cas_latency: 36,
    },
    '4': {
        // Storage
        type: 'SSD',
        cache: '2048 MB',
        capacity: '2 TB',
        interface: 'M.2 PCIe 4.0 X4',
        form_factor: 'M.2-2280',
    },
    '5': {
        // PSU
        type: 'ATX',
        modular: 'Full',
        wattage: '750 W',
        efficiency_rating: '80+ Gold',
    },
    '6': {
        // Case
        type: 'MicroATX Mini Tower',
        color: 'Black',
        side_panel: 'Tempered Glass',
        internal_bays: '2',
        external_volume: '31.67 L',
    },
    '7': {
        // GPU
        color: 'Black',
        length: '281 mm',
        memory: '16 GB',
        chipset: 'Radeon RX 9060 XT',
        core_clock: '2220 MHz',
        boost_clock: '3320 MHz',
    },
    '8': {
        // CPU Cooler
        color: 'Black / Silver',
        fan_rpm: '1550 RPM',
        noise_level: '25.6 dB',
        radiator_size: 'None',
    },
};

export const specsCategoryNames: Record<string, string> = {
    '1': 'Motherboard',
    '2': 'CPU',
    '3': 'RAM',
    '4': 'Storage',
    '5': 'PSU',
    '6': 'Case',
    '7': 'GPU',
    '8': 'CPU Cooler',
};
