import {Controller} from '@hotwired/stimulus'
import {
    buildOrderPreview,
    formatPriceCents,
    shouldShowEquipmentLoanFields,
} from '../modules/order/order_preview'

export default class extends Controller {
    static targets = [
        'form',
        'peopleCountField',
        'deliveryCityField',
        'distanceKmField',
        'needEquipmentLoanField',
        'loanFields',
        'discountHint',
        'summaryPeople',
        'summarySubtotal',
        'summaryDiscount',
        'summaryDelivery',
        'summaryTotal',
    ]

    static values = {
        basePrice: Number,
        minPeople: Number,
        hiddenClass: {type: String, default: 'd-none'},
    }

    connect() {
        this.sync()
    }

    sync() {
        const preview = buildOrderPreview({
            basePriceCents: this.basePriceValue,
            minimumPeople: this.minPeopleValue,
            peopleCount: this.hasPeopleCountFieldTarget ? this.peopleCountFieldTarget.value : this.minPeopleValue,
            deliveryCity: this.hasDeliveryCityFieldTarget ? this.deliveryCityFieldTarget.value : '',
            distanceKm: this.hasDistanceKmFieldTarget ? this.distanceKmFieldTarget.value : 0,
            needEquipmentLoan: this.hasNeedEquipmentLoanFieldTarget && this.needEquipmentLoanFieldTarget.checked,
        })

        this.renderSummary(preview)
        this.syncEquipmentLoanFields(preview.needEquipmentLoan)
        this.syncDiscountHint(preview.discountActive)
    }

    renderSummary(preview) {
        if (this.hasSummaryPeopleTarget) {
            this.summaryPeopleTarget.textContent = String(preview.peopleCount)
        }

        if (this.hasSummarySubtotalTarget) {
            this.summarySubtotalTarget.textContent = formatPriceCents(preview.menuSubtotal)
        }

        if (this.hasSummaryDiscountTarget) {
            this.summaryDiscountTarget.textContent = formatPriceCents(preview.discountAmount * -1)
        }

        if (this.hasSummaryDeliveryTarget) {
            this.summaryDeliveryTarget.textContent = formatPriceCents(preview.deliveryPrice)
        }

        if (this.hasSummaryTotalTarget) {
            this.summaryTotalTarget.textContent = formatPriceCents(preview.totalPrice)
        }
    }

    syncEquipmentLoanFields(needEquipmentLoan) {
        if (!this.hasLoanFieldsTarget) {
            return
        }

        this.loanFieldsTarget.classList.toggle(
            this.hiddenClassValue,
            !shouldShowEquipmentLoanFields({needEquipmentLoan}),
        )
    }

    syncDiscountHint(discountActive) {
        if (!this.hasDiscountHintTarget) {
            return
        }

        this.discountHintTarget.classList.toggle(this.hiddenClassValue, !discountActive)
    }
}
