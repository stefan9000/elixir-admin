@extends('_layout.admin.master')
@section('content')
    <div class="app-container" id="root">
        <!--<h1>@{{ title }}</h1> -->
        <form @submit.prevent="sendData()">
            <div class="tabs">
                <button type="button" @click="switchSection(0)">Information</button>
                <button type="button" @click="switchSection(1)">Location</button>
                <button type="button" @click="switchSection(2)">Media</button>
                <button type="button" @click="switchSection(3)">Tickets and pricing</button>
            </div>

            <div class="form-hold" v-show="currentSection === 0">
                <div class="language">
                    <div class="language-row">
                        <h3>Select Language</h3>
                        <button type="button" @click="switchLanguage('en')">EN</button>
                        <button type="button" @click="switchLanguage('rs')">RS</button>
                        <button type="button" @click="switchLanguage('de')">DE</button>
                        <button type="button" @click="switchLanguage('fr')">FR</button>
                    </div>
                </div>
                
                <div class="main-info" v-for="language in languages" v-show="language === currentLanguage">
                    <h3>Selected language: @{{ language.toUpperCase() }}</h3>
                    <label>Event name</label>
                    <input placeholder="eg: Saturday Night" type="text" v-model="translations[language].name"/>
                    <label>Description</label>
                    <textarea placeholder="eg: No other place to be." v-model="translations[language].description" cols="30" rows="5"></textarea>
                </div>
                <label>Artist</label>
                <input type="text" v-model="entry.artist"/>

                <div class="some-row">
                    <div class="some-group">
                        <label>Starting date</label>
                        <input type="date" v-model="entry.start_date"/>
                        <label>Starting time</label>
                        <input placeholder="eg: 23:00"  type="text" v-model="entry.start_time"/>
                    </div>

                    <div class="some-group">
                        <label>Ending date</label>
                        <input type="date" v-model="entry.end_date"/>
                        <label>Ending time</label>
                        <input placeholder="eg: 05:00" type="text" v-model="entry.end_time"/>
                    </div>
                </div>

            </div>
            <div class="form-hold" v-show="currentSection === 1">
                <div id="map" style="height: 400px; margin-bottom: 1rem"></div>
                <label>Location</label>
                <input type="text" v-model="entry.location"/>
                <label>Latitude</label>
                <input type="text" v-model="entry.latitude" onfocus="blur();"/>
                <label>Longitude</label>
                <input type="text" v-model="entry.longitude" onfocus="blur();"/>
                <input type="hidden" v-model="entry.zoom"/>
            </div>
            <div class="form-hold" v-show="currentSection === 2">
                <label>Video</label>

                <div class="some-row">
                    <input class="form-file" type="file" ref="video" @change="setVideoAttribute()" accept=".mp4,.webm"/>
                    <a class="preview" :href="uploadedVideo" v-show="uploadedVideo" target="_blank">View</a>
                </div>
            
                <label>Thumbnail</label>
                <div class="some-row">
                    <input class="form-file" type="file" ref="thumbnail" @change="setThumbnailAttribute()" accept=".jpg,.jpeg,.png"/>
                    <a class="preview" :href="uploadedThumbnail" v-show="uploadedThumbnail" target="_blank">View</a>
                </div>
                
                <label>Images</label>
                <input class="form-file" type="file" ref="images" @change="setImagesAttribute()" accept=".jpg,.jpeg,.png" multiple/>
                <div  class="gallery-holder">
                    <div v-for="(image, index) in uploadedImages">
                        <button type="button" @click="deleteImage(image.id, index)">X</button>
                        <img :src="image.src"/>
                    </div>
                </div>
                
            </div>
            <div class="form-hold" v-show="currentSection === 3">
                <h3>Tickets and prices</h3>
                <div>
                    <div class="some-row">
                        <div class="some-group">
                            <label>Starting tickets</label>
                            <input placeholder="eg: 100" type="text" v-model="entry.starting_tickets"/>
                        </div>
                        <div class="some-group">
                            <label>Price</label>
                            <input placeholder="99" type="text" v-model="entry.starting_price"/>
                        </div>
                    </div>

                    <div class="some-row">
                        <div class="some-group">
                            <label>Mid tickets</label>
                            <input placeholder="eg: 200" type="text" v-model="entry.mid_tickets"/>
                        </div>
                        <div class="some-group">
                            <label>Mid price</label>
                            <input placeholder="eg: 199" type="text" v-model="entry.mid_price"/>
                        </div>
                    </div>

                    <div class="some-row">
                        <div class="some-group">
                            <label>End tickets</label>
                            <input placeholder="eg: 300" type="text" v-model="entry.end_tickets"/>
                        </div>
                        <div class="some-group">
                            <label>End price</label>
                            <input placeholder="eg: 299" type="text" v-model="entry.end_price"/>
                        </div>
                    </div>
                </div>
            </div>
            <span class="success-msg" v-show="success">Successfully saved your changes.</span>
            <span class="warning-msg" v-show="submitError">Please correct all the errors before submitting.</span>
            
            <div class="error-list">
                <div class="single-error" v-for="errors in errorList">
                    <span v-for="error in errors">@{{ error }}</span>
                </div>
            </div>
            <button class="submit" type="submit">Create Event</button>


        </form>
    </div>
@endsection
@section('scripts')
    <script>
        var map;
        function initMap() {
            var geocoder = new google.maps.Geocoder;
            let defaultLat = 47.371624590496474;
            let defaultLng = 8.54412289858121;
            let defaultZoom = 12;

            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: defaultLat, lng: defaultLng},
                zoom: defaultZoom
            });
            map.addListener('click', function(event) {
                app.entry.latitude = event.latLng.lat();
                app.entry.longitude = event.latLng.lng();
                app.entry.zoom = map.getZoom();

                map.setCenter({
                    lat: app.entry.latitude,
                    lng: app.entry.longitude
                });
                geocode(app.entry.latitude, app.entry.longitude);
            });

            function geocode(lat, lng) {
                let latLng = {lat: parseFloat(lat), lng: parseFloat(lng)};

                geocoder.geocode({'location': latLng}, function(results, status) {
                    if (status === 'OK') {
                        console.log(results);
                        console.log(status);
                    } else {
                        console.error('Something went wrong.');
                    }
                });
            }
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api') }}&callback=initMap"
            async defer></script>
    <script type="text/javascript">
        let app = new Vue({
            el: '#root',
            data: {
                title: 'Creating a new event',
                url: '/api/events/store',
                method: 'POST',
                languages: ['en', 'rs', 'de', 'fr'],
                currentSection: 0,
                currentLanguage: 'en',
                entry: {
                    start_date: '',
                    start_time: '',
                    end_date: '',
                    end_time: '',
                    starting_tickets: '',
                    starting_price: '',
                    mid_tickets: '',
                    mid_price: '',
                    end_tickets: '',
                    end_price: '',
                    artist: '',
                    location: '',
                    longitude: '',
                    latitude: '',
                    zoom: '',
                    video: '',
                    thumbnail: ''
                },
                images: [],
                translations: {},
                uploadedVideo: '',
                uploadedThumbnail: '',
                uploadedImages: '',
                success: false,
                submitError: false,
                errorList: {},
            },
            methods: {
                switchSection(section) {
                    this.currentSection = section;
                },
                switchLanguage(language) {
                    this.currentLanguage = language;
                },
                setVideoAttribute() {
                    this.entry.video = event.target.files[0];
                },
                setThumbnailAttribute() {
                    this.entry.thumbnail = event.target.files[0];
                },
                setImagesAttribute() {
                    this.images = event.target.files;
                },
                deleteImage(image_id, index) {
                    let element = event.target;

                    this.$http({
                        url: '/api/events/' + this.id + '/images/' + image_id,
                        method: 'DELETE',
                    })
                    .then(() => {
                        this.uploadedImages.splice(index, 1);
                    });
                },
                sendData() {
                    let formData = new FormData();
                    formData.append('thumbnail', this.entry.thumbnail);
                    formData.append('video', this.entry.video);
                    formData.append('start_date', this.entry.start_date);
                    formData.append('start_time', this.entry.start_time);
                    formData.append('end_date', this.entry.end_date);
                    formData.append('end_time', this.entry.end_time);
                    formData.append('latitude', this.entry.latitude);
                    formData.append('longitude', this.entry.longitude);
                    formData.append('location', this.entry.location);
                    formData.append('zoom', this.entry.zoom);
                    formData.append('artist', this.entry.artist);
                    formData.append('starting_tickets', this.entry.starting_tickets);
                    formData.append('starting_price', this.entry.starting_price);
                    formData.append('mid_tickets', this.entry.mid_tickets);
                    formData.append('mid_price', this.entry.mid_price);
                    formData.append('end_tickets', this.entry.end_tickets);
                    formData.append('end_price', this.entry.end_price);

                    formData.append('translations[en][name]', this.translations.en.name);
                    formData.append('translations[en][description]', this.translations.en.description);
                    formData.append('translations[rs][name]', this.translations.rs.name);
                    formData.append('translations[rs][description]', this.translations.rs.description);
                    formData.append('translations[de][name]', this.translations.de.name);
                    formData.append('translations[de][description]', this.translations.de.description);
                    formData.append('translations[fr][name]', this.translations.fr.name);
                    formData.append('translations[fr][description]', this.translations.fr.description);
                    formData.append('_method', this.method);

                    for (let i = 0; i < this.images.length; i++) {
                        formData.append('images['+ i +']', this.images[i]);
                    }

                    this.success = false;
                    this.submitError = false;
                    this.errorList = {};

                    this.$http({
                        url: this.url,
                        method: 'POST',
                        data: formData
                    })
                    .then((response) => {
                        let data = response.data;
                        this.id = data.id;
                        this.uploadedImages = data.images;
                        this.uploadedVideo = data.video_src;
                        this.uploadedThumbnail = data.thumbnail;


                        this.url = '/api/events/' + this.id;
                        this.method = 'PATCH';

                        this.$refs.thumbnail.value = '';
                        this.$refs.video.value = '';
                        this.$refs.images.value = '';

                        this.title = 'Editing "'+ data.name +'"';
                        this.success = true;
                    })
                    .catch((error) => {
                        this.submitError = true;

                        if (error.response.data != null) {
                            this.errorList = error.response.data.errors;
                            console.log(this.errorList);
                        }
                    });
                }
            },
            created() {
                for (let i = 0; i < this.languages.length; i++) {
                    this.translations[this.languages[i]] = {
                        name: '',
                        description: ''
                    };
                }
            }
        });
    </script>
@endsection
