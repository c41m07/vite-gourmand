import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'peopleCount',
        'deliveryCity',
        'distanceKm',
        'summaryPeople',
        'summarySubtotal',
        'summaryDiscount',
        'deliveryPrice',
        'summaryTotal',
        'needEquipmentLoan',
        'equipmentLoanStartAt',
        'equipmentLoanEndAt',
        'equipmentLoanField',
        'summaryLoan',
        'submitButton'
    ];

    static values = {
        basePrice: Number,
        minPeople: Number,
        stock: Number,
    };

    connect() {
        this.refresh();
    }

    refresh() {
        const state = this.readState();
        const result = this.calculate(state);
        const isValid = this.validate(state);

        this.render(result, isValid);
    }

    readState() {
        const peopleCount = Number.parseInt(this.peopleCountTarget.value || '0', 10);
        const deliveryCity = this.deliveryCityTarget.value.trim();
        const normalizedCity = deliveryCity.toLowerCase();
        const distanceKm = Math.max(0, Number.parseInt(this.distanceKmTarget.value || '0', 10));
        const needEquipmentLoan = this.needEquipmentLoanTarget.checked;
        const equipmentLoanStartAt = this.equipmentLoanStartAtTarget.value;
        const equipmentLoanEndAt = this.equipmentLoanEndAtTarget.value;

        return {
            peopleCount,
            deliveryCity,
            normalizedCity,
            distanceKm,
            needEquipmentLoan,
            equipmentLoanStartAt,
            equipmentLoanEndAt,
        };
    }

    calculate(state) {
        const isBordeaux = state.normalizedCity === 'bordeaux';
        const effectiveDistanceKm = isBordeaux ? 0 : state.distanceKm;
        const menuSubtotal = state.peopleCount * this.basePriceValue;
        const discountApplied = state.peopleCount >= (this.minPeopleValue + 5);
        const discountAmount = discountApplied ? Math.round(menuSubtotal * 0.10) : 0;
        const deliveryPrice = isBordeaux ? 0 : 500 + (effectiveDistanceKm * 59);
        const totalPrice = menuSubtotal - discountAmount + deliveryPrice;
        const formattedStartAt = this.formatDateTime(state.equipmentLoanStartAt);
        const formattedEndAt = this.formatDateTime(state.equipmentLoanEndAt);

        let loanText = 'Aucun';

        if (state.needEquipmentLoan) {
            if (state.equipmentLoanStartAt && state.equipmentLoanEndAt) {
                loanText = `Du ${formattedStartAt} au ${formattedEndAt}`;
            } else {
                loanText = 'Pret de materiel demande';
            }
        }

        return {
            peopleCount: state.peopleCount,
            isBordeaux,
            effectiveDistanceKm,
            menuSubtotal,
            discountApplied,
            discountAmount,
            deliveryPrice,
            totalPrice,
            loanText,
            needEquipmentLoan: state.needEquipmentLoan,
        };
    }

    validate(state) {
        const peopleCountValid =
            state.peopleCount >= this.minPeopleValue &&
            state.peopleCount <= this.stockValue;
        const deliveryCityValid = state.deliveryCity !== '';
        const distanceKmValid = state.distanceKm >= 0;

        let equipmentLoanValid = true;
        if (state.needEquipmentLoan) {
            equipmentLoanValid =
                state.equipmentLoanStartAt !== '' &&
                state.equipmentLoanEndAt !== '' &&
                state.equipmentLoanEndAt >= state.equipmentLoanStartAt;
        }

        return peopleCountValid && deliveryCityValid && distanceKmValid && equipmentLoanValid;
    }

    render(result, isValid) {
        this.summaryPeopleTarget.textContent = `Menu (${result.peopleCount} pers.)`;
        this.summarySubtotalTarget.textContent = this.formatPrice(result.menuSubtotal);
        this.summaryDiscountTarget.textContent = `- ${this.formatPrice(result.discountAmount)}`;
        this.deliveryPriceTarget.textContent = this.formatPrice(result.deliveryPrice);
        this.summaryTotalTarget.textContent = this.formatPrice(result.totalPrice);
        this.equipmentLoanFieldTarget.classList.toggle('d-none', !result.needEquipmentLoan);
        this.summaryLoanTarget.textContent = result.loanText;
        this.submitButtonTarget.disabled = !isValid;
    }

    formatPrice(cents) {
        return `${(cents / 100).toFixed(2).replace('.', ',')} €`;
    }

    formatDateTime(value) {
        if (!value) {
            return '';
        }

        const date = new Date(value);

        if (Number.isNaN(date.getTime())) {
            return value;
        }

        return new Intl.DateTimeFormat('fr-FR', {
            dateStyle: 'short',
        }).format(date);
    }
}
