export const formatCurrency = (amount: string | number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'PHP',
    }).format(Number(amount || 0));
};

export const formatAddress = (address?: Record<string, any>) => {
    if (!address) return 'N/A';
    return [
        address.address,
        address.barangay,
        address.city,
        address.province,
        address.region,
    ]
        .filter(Boolean)
        .join(', ');
};

