export function normalizeInteger(value, fallback = 0) {
    const normalizedValue = String(value ?? '').trim()

    if (normalizedValue === '') {
        return fallback
    }

    const parsedValue = Number.parseInt(normalizedValue, 10)

    return Number.isNaN(parsedValue) ? fallback : parsedValue
}

export function isBordeauxCity(city) {
    return String(city ?? '').trim().toLowerCase() === 'bordeaux'
}

export function shouldShowEquipmentLoanFields({needEquipmentLoan}) {
    return Boolean(needEquipmentLoan)
}

export function buildOrderPreview({
    basePriceCents,
    minimumPeople,
    peopleCount,
    deliveryCity,
    distanceKm,
    needEquipmentLoan = false,
}) {
    const normalizedBasePrice = Math.max(0, normalizeInteger(basePriceCents))
    const normalizedMinimumPeople = Math.max(0, normalizeInteger(minimumPeople))
    const normalizedPeopleCount = Math.max(0, normalizeInteger(peopleCount, normalizedMinimumPeople))
    const normalizedDistanceKm = Math.max(0, normalizeInteger(distanceKm))
    const menuSubtotal = normalizedBasePrice * normalizedPeopleCount
    const discountAmount = normalizedPeopleCount >= normalizedMinimumPeople + 5
        ? Math.round(menuSubtotal * 0.10)
        : 0
    const deliveryPrice = isBordeauxCity(deliveryCity)
        ? 0
        : 500 + (normalizedDistanceKm * 59)

    return {
        peopleCount: normalizedPeopleCount,
        menuSubtotal,
        discountAmount,
        deliveryPrice,
        totalPrice: menuSubtotal - discountAmount + deliveryPrice,
        discountActive: discountAmount > 0,
        needEquipmentLoan: Boolean(needEquipmentLoan),
    }
}

export function formatPriceCents(amountCents) {
    const normalizedAmount = normalizeInteger(amountCents)
    const sign = normalizedAmount < 0 ? '-' : ''
    const absoluteAmount = Math.abs(normalizedAmount)
    const amountInEuros = (absoluteAmount / 100).toFixed(2)
    const [wholePart, decimalPart] = amountInEuros.split('.')
    const formattedWholePart = wholePart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ')

    return `${sign}${formattedWholePart},${decimalPart} €`
}
