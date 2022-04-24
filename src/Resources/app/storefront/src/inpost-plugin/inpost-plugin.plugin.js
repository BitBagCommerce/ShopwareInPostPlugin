import Plugin from 'src/plugin-system/plugin.class';

export default class InpostPluginPlugin extends Plugin {
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
    }

    updateSelected(point) {
        const inpostParcelLockerEl = document.querySelector('#inpost-parcel-locker');
        const selectedPoint = document.querySelector('[data-inpost-plugin-selectedPoint]');
        const InpostGeoMap = document.querySelector('[data-inpost-plugin-map]');
        const pointThumbnail = document.querySelector('[data-inpost-plugin-thumbnail]');
        const pointName = document.querySelector('[data-inpost-plugin-name]');
        const pointAddressOne = document.querySelector('[data-inpost-plugin-addressOne]');
        const pointAddressTwo = document.querySelector('[data-inpost-plugin-addressTwo]');

        selectedPoint.classList.remove('hide');
        InpostGeoMap.classList.add('hide');
        inpostParcelLockerEl.value = point.name;
        pointThumbnail.src = point.image_url
        pointName.innerText = point.name;
        pointAddressOne.innerText = point.address.line1;
        pointAddressTwo.innerText = point.address.line2;
    }

    changePoint() {
        const selectedPoint = document.querySelector('[data-inpost-plugin-selectedPoint]');
        const InpostGeoMap = document.querySelector('[data-inpost-plugin-map]');

        selectedPoint.classList.add('hide');
        InpostGeoMap.classList.remove('hide');
    }
}
