import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['form', 'list', 'count', 'priceMinValue', 'priceMaxValue', 'peopleValue'];
    async fetchMenus(){

        const queryString = this.buildParams();
        const url = queryString ? `/api/menus?${queryString}` : '/api/menus';
        const response = await fetch(url);
        const data = await response.json();
        this.listTarget.innerHTML = data.body;
        this.updateCount(data.count);

    }
    convertToCents = (value)=>{
        const number = parseFloat( String(value).replace(',', '.'));
        return Number.isNaN(number) ? '' : number * 100;
    };

    buildParams(){

        const data= new FormData(this.formTarget);
        const params = new URLSearchParams();
        const minPrice = this.convertToCents(data.get('minPrice'));
        const maxPrice = this.convertToCents(data.get('maxPrice'));
        const minPersons = data.get('minPersons');
        const theme = data.get('theme');
        const diet = data.get('diet');

        if (minPrice !== '') params.set('minPrice', minPrice);
        if (maxPrice !== '') params.set('maxPrice', maxPrice);
        if (minPersons && minPersons !== '0') params.set('minPersons', minPersons);
        if (theme) params.set('theme', theme)
        if (diet) params.set ('diet',diet)

        return params.toString();

    }

    connect() {
        this.updateLabels();
        this.updateCount();
    }

     async filter() {
        this.updateLabels();
        this.updateCount();
        await this.fetchMenus();
    }

    reset() {
            this.updateLabels();
            this.updateCount();
    }

    updateLabels() {
        this.priceMinValueTarget.textContent = `${this.formTarget.minPrice.value}€`;
        this.priceMaxValueTarget.textContent = `${this.formTarget.maxPrice.value}€`;
        const peopleValue = this.formTarget.minPersons.value;
        this.peopleValueTarget.textContent = peopleValue === '0' ? 'Tous' : peopleValue;
    }

    updateCount() {
        if (!this.hasCountTarget || !this.hasListTarget) {
            return;
        }

        const count = this.listTarget.querySelectorAll('.menu-card').length;
        const current = this.countTarget.textContent || '';

        if (/\d+/.test(current)) {
            this.countTarget.textContent = current.replace(/\d+/, count);
        } else {
            this.countTarget.textContent = `${count} menus`;
        }
    }



}
