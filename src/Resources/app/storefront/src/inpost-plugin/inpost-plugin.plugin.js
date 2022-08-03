import Plugin from 'src/plugin-system/plugin.class';

export default class InPostPlugin extends Plugin {
    init() {
        window.afterPointSelected = this.updateSelected;

        const changePointButton = document.querySelector('[data-inpost-plugin-changePoint]');
        changePointButton.addEventListener('click', (e) => {
            e.preventDefault();
            this.changePoint();
        });

        const confirmFormSubmitButtonEl = document.getElementById('confirmFormSubmit');
        if (confirmFormSubmitButtonEl) {
            confirmFormSubmitButtonEl.addEventListener('click', (e) => {
                const pointNameNotSelectedMessageEl = document.querySelector('.point-name-not-selected-message');

                const inPostParcelLockerEl = document.getElementById('inpost-parcel-locker');
                if (!inPostParcelLockerEl || (inPostParcelLockerEl && !inPostParcelLockerEl.value)) {
                    e.preventDefault();

                    document.querySelector('.shipping-methods').scrollIntoView({
                        behavior: 'smooth'
                    });

                    if (pointNameNotSelectedMessageEl) {
                        pointNameNotSelectedMessageEl.classList.remove('hide');
                    }
                } else {
                    if (pointNameNotSelectedMessageEl) {
                        pointNameNotSelectedMessageEl.classList.add('hide');
                    }
                }
            });
        }
    }

    updateSelected(point) {
        const inPostParcelLockerEl = document.querySelector('#inpost-parcel-locker');
        inPostParcelLockerEl.value = point.name;

        const selectedPoint = document.querySelector('[data-inpost-plugin-selectedPoint]');
        selectedPoint.classList.remove('hide');

        const inPostGeoMap = document.querySelector('[data-inpost-plugin-map]');
        inPostGeoMap.classList.add('hide');

        const pointThumbnail = document.querySelector('[data-inpost-plugin-thumbnail]');
        pointThumbnail.src = point.image_url

        const pointName = document.querySelector('[data-inpost-plugin-name]');
        pointName.innerText = point.name;

        const pointAddressOne = document.querySelector('[data-inpost-plugin-addressOne]');
        pointAddressOne.innerText = point.address.line1;

        const pointAddressTwo = document.querySelector('[data-inpost-plugin-addressTwo]');
        pointAddressTwo.innerText = point.address.line2;

        const pointNameNotSelectedMessageEl = document.querySelector('.point-name-not-selected-message');
        if (pointNameNotSelectedMessageEl) {
            pointNameNotSelectedMessageEl.classList.add('hide');
        }
    }

    changePoint() {
        const selectedPoint = document.querySelector('[data-inpost-plugin-selectedPoint]');
        selectedPoint.classList.add('hide');

        const inPostGeoMap = document.querySelector('[data-inpost-plugin-map]');
        inPostGeoMap.classList.remove('hide');
    }
}
