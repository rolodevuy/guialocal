{{--
    MapPicker — Filament form component
    La lógica va en <script> para evitar conflictos de comillas en x-data.
    Carga Leaflet dinámicamente, permite click + drag para fijar lat/lng.
--}}

<script>
    window.mapPickerData = function () {
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
                var lat = parseFloat(this.$wire.data && this.$wire.data.lat) || this.defaultCenter[0];
                var lng = parseFloat(this.$wire.data && this.$wire.data.lng) || this.defaultCenter[1];
                var hasCoords = !!(this.$wire.data && this.$wire.data.lat && this.$wire.data.lng);

                this.map = L.map(this.$refs.mapel, {
                    center: [lat, lng],
                    zoom: hasCoords ? 16 : 14,
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

                    self.$wire.set('data.lat', newLat);
                    self.$wire.set('data.lng', newLng);
                });

                // Scroll zoom solo cuando el mouse está sobre el mapa
                var container = this.map.getContainer();
                container.addEventListener('mouseenter', function () { self.map.scrollWheelZoom.enable(); });
                container.addEventListener('mouseleave', function () { self.map.scrollWheelZoom.disable(); });
            },

            bindDrag(marker) {
                var self = this;
                marker.on('dragend', function (e) {
                    var pos = e.target.getLatLng();
                    self.$wire.set('data.lat', parseFloat(pos.lat.toFixed(7)));
                    self.$wire.set('data.lng', parseFloat(pos.lng.toFixed(7)));
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
    x-data="mapPickerData()"
    wire:ignore
    class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm"
    style="height: 320px;"
>
    <div x-ref="mapel" style="height: 320px; width: 100%;"></div>
</div>

<p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
    Hacé click en el mapa para fijar la ubicación. El marcador se puede arrastrar para ajustar.
</p>
