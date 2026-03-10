{{--
    MapPicker — variante para Zonas
    Actualiza data.lat_centro y data.lng_centro (en vez de lat/lng).
--}}

<script>
    window.mapPickerZonaData = function () {
        return {
            map: null,
            marker: null,
            defaultCenter: [-34.7667, -55.7621],

            init() {
                var self = this;
                this.loadLeaflet().then(function () {
                    self.setupMap();
                });
            },

            setupMap() {
                var self = this;
                var lat = parseFloat(this.$wire.data && this.$wire.data.lat_centro) || this.defaultCenter[0];
                var lng = parseFloat(this.$wire.data && this.$wire.data.lng_centro) || this.defaultCenter[1];
                var hasCoords = !!(this.$wire.data && this.$wire.data.lat_centro && this.$wire.data.lng_centro);

                this.map = L.map(this.$refs.mapel, {
                    center: [lat, lng],
                    zoom: hasCoords ? 14 : 12,
                    scrollWheelZoom: false,
                    zoomControl: true,
                });

                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    attribution: '\u00a9 OpenStreetMap contributors \u00a9 CARTO',
                    subdomains: 'abcd',
                    maxZoom: 19,
                }).addTo(this.map);

                if (hasCoords) {
                    this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);
                    this.bindDrag(this.marker);
                }

                this.map.on('click', function (e) {
                    var newLat = parseFloat(e.latlng.lat.toFixed(7));
                    var newLng = parseFloat(e.latlng.lng.toFixed(7));

                    if (self.marker) {
                        self.marker.setLatLng(e.latlng);
                    } else {
                        self.marker = L.marker(e.latlng, { draggable: true }).addTo(self.map);
                        self.bindDrag(self.marker);
                    }

                    self.$wire.set('data.lat_centro', newLat);
                    self.$wire.set('data.lng_centro', newLng);
                });

                var container = this.map.getContainer();
                container.addEventListener('mouseenter', function () { self.map.scrollWheelZoom.enable(); });
                container.addEventListener('mouseleave', function () { self.map.scrollWheelZoom.disable(); });
            },

            bindDrag(marker) {
                var self = this;
                marker.on('dragend', function (e) {
                    var pos = e.target.getLatLng();
                    self.$wire.set('data.lat_centro', parseFloat(pos.lat.toFixed(7)));
                    self.$wire.set('data.lng_centro', parseFloat(pos.lng.toFixed(7)));
                });
            },

            loadLeaflet() {
                return new Promise(function (resolve) {
                    if (window.L) { resolve(); return; }

                    if (!document.querySelector('link[href*="leaflet@1.9.4"]')) {
                        var link = document.createElement('link');
                        link.rel  = 'stylesheet';
                        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                        document.head.appendChild(link);
                    }

                    if (!document.querySelector('script[src*="leaflet@1.9.4"]')) {
                        var script  = document.createElement('script');
                        script.src  = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        script.onload = resolve;
                        document.head.appendChild(script);
                    } else {
                        resolve();
                    }
                });
            },
        };
    };
</script>

<div
    x-data="mapPickerZonaData()"
    wire:ignore
    class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm"
    style="height: 320px;"
>
    <div x-ref="mapel" style="height: 320px; width: 100%;"></div>
</div>

<p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
    Hacé click en el mapa para fijar el centro de la zona. El marcador se puede arrastrar para ajustar.
</p>
