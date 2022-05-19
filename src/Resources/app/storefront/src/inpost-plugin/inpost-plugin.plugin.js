import Plugin from 'src/plugin-system/plugin.class';

export default class InPostPlugin extends Plugin {
    init() {
        const main = this;

        window.easyPackAsyncInit = () => {
            easyPack.init({});
            easyPack.mapWidget('easypack-map', (point) => {
                main.updateSelected(point);
            });
        }

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
        const selectedPoint = document.querySelector('[data-inpost-plugin-selectedPoint]');
        const inPostGeoMap = document.querySelector('[data-inpost-plugin-map]');
        const pointThumbnail = document.querySelector('[data-inpost-plugin-thumbnail]');
        const pointName = document.querySelector('[data-inpost-plugin-name]');
        const pointAddressOne = document.querySelector('[data-inpost-plugin-addressOne]');
        const pointAddressTwo = document.querySelector('[data-inpost-plugin-addressTwo]');
        const pointNameNotSelectedMessageEl = document.querySelector('.point-name-not-selected-message');

        selectedPoint.classList.remove('hide');
        inPostGeoMap.classList.add('hide');
        inPostParcelLockerEl.value = point.name;
        pointThumbnail.src = point.image_url
        pointName.innerText = point.name;
        pointAddressOne.innerText = point.address.line1;
        pointAddressTwo.innerText = point.address.line2;

        if (pointNameNotSelectedMessageEl) {
            pointNameNotSelectedMessageEl.classList.add('hide');
        }
    }

    changePoint() {
        const selectedPoint = document.querySelector('[data-inpost-plugin-selectedPoint]');
        const inPostGeoMap = document.querySelector('[data-inpost-plugin-map]');

        selectedPoint.classList.add('hide');
        inPostGeoMap.classList.remove('hide');
    }
}
