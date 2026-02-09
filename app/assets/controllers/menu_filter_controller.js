import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['form', 'list', 'count', 'priceMinValue', 'priceMaxValue', 'peopleValue'];
    async fetchMenus(){

        const queryString = this.buildParams();
        const url = queryString ? `/api/menus?${queryString}` : '/api/menus';
        const response = await fetch(url);
        const menus = await response.json();
        this.render(menus);
        this.updateCount(menus.length);

    }
    convertToCents = (value)=>{
        const number = parseFloat( String(value).replace(',', '.'));
        return Number.isNaN(number) ? '' : number * 100;
    };

    render(items){
        const formatPrice = (cents) => {
            const value = Number(cents) || 0;
            const euros = (value / 100).toFixed(2).replace('.', ',');
            return `${euros}€`;
        };


        // TODO: a revoir pour éviter injection //
        if (!items || items.length === 0) {
            this.listTarget.innerHTML = '<p>Pas de menu disponible</p>';
            return;
        }

        // TODO: a revoir pour éviter injection //
        const html = items.map((menu) => `
          <div class="col-12 col-md-6 col-lg-4 menu-card">
            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
              ${menu.mediaUrl
                  ? `<img src="${menu.mediaUrl}" class="card-img-top menu-card__img" alt="${menu.mediaAltText ? menu.mediaAltText : ''}">`
                  : '<div class="menu-card__img bg-primary"></div>'}
              <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start gap-3">
                  <h3 class="card-title h5 fw-bold mb-0">${menu.title}</h3>
                  <span class="fw-bold text-success">${formatPrice(menu.basePrice)}</span>
                </div>
                <p class="text-muted small mb-2">Minimum ${menu.minPeople} personnes</p>
                ${menu.description ? `<p class="card-text text-muted mb-4">${menu.description}</p>` : ''}
                <a href="/menu/${menu.id}" class="btn btn-outline-success mt-auto align-self-center px-4">Voir detail</a>
              </div>
            </div>
          </div>
        `).join('');

        this.listTarget.innerHTML = html;

    }

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

    filter() {
        this.updateLabels();
        this.updateCount();
        this.fetchMenus();
    }

    reset() {
        setTimeout(() => {
            this.updateLabels();
            this.updateCount();
        }, 0);
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
