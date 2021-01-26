@extends('_layout.admin.master')
@section('content')
    <div class="app-container news-container" id="root">
        <!--<h1>@{{ title }}</h1> -->
        <form @submit.prevent="sendData()">
            <div class="tabs news-tabs">
                <button type="button" @click="selectedSection = 0">Content</button>
                <button type="button" @click="selectedSection = 1">Images</button>
            </div>
            <div class="form-hold" v-show="selectedSection === 0">
                <div class="some-row">
                    <div class="some-group">
                        <label>Published on</label>
                        <input type="date" v-model="published_on" value="{{ date('Y-m-d') }}"/>
                    </div>

                    <div class="some-group">
                        <label>Thumbnail</label>
                        <div class="form-row">
                            <input class="form-file" type="file" @change="setThumbnailAttribute()" ref="thumbnail" accept=".jpg,.jpeg,.png"/>
                            <a class="preview" :href="uploadedThumbnail" v-show="uploadedThumbnail" target="_blank">View</a>
                        </div>
                    </div>

                    <div class="some-group">
                        <label>Video</label>
                        <div class="form-row">
                            <input class="form-file" type="file" @change="setVideoAttribute()" ref="video" accept=".mp4,.webm"/>
                            <a class="preview" :href="uploadedVideo" v-show="uploadedVideo" target="_blank">View</a>
                        </div>
                    </div>

                </div>
                <div class="language">
                    <div class="language-row">
                        <h3>Select Language:</h3>
                        <button type="button" v-for="(language) in languages" @click="selectLanguage(language)">@{{ language }}</button>
                    </div>
                </div>

                <div class="main-info"  v-for="language in languages" v-show="selectedLanguage === language">
                    <h3>Creating Content for:  @{{ language.toUpperCase() }} language</h3>
                    <label>Title</label>
                    <input type="text" v-model="translations[language].title"/>
                </div>
                <label>Body</label>
                <ckeditor :editor="editor" v-model="editorBody" :config="editorConfig" @input="setTranslationContent()"></ckeditor>
            </div>
            <div v-show="selectedSection === 1">
                <label>Choose files to upload</label>
                <input class="form-file" type="file" ref="images" @change="setImagesAttribute()" accept=".jpg,.jpeg,.png,.webp" multiple/>
                <div  class="gallery-holder">
                    <div v-for="(image, index) in uploadedImages">
                        <button type="button" @click="deleteImage(image.id, index)">Remove</button>
                        <img :src="image.src"/>
                    </div>
                </div>
            </div>
            <span class="success-msg" v-show="success">Successfully saved your changes.</span>
            <span class="warning-msg" v-show="submitError">Please correct all the errors before submitting.</span>
            <button class="submit" type="submit">Save</button>

        </form>
    </div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/ckeditor5/ckeditor.js') }}"></script>
<script type="text/javascript">
let app = new Vue({
    el: '#root',
    data: {
        id: '',
        title: 'Creating a new news entry.',
        editor: ClassicEditor,
        editorConfig: {
            simpleUpload: {
                uploadUrl: '/api/news/ckeditor/image',
                headers: {
                    Authorization: 'Bearer ' + document.getElementById('api_token').value,
                    Accept: 'application/json'
                }
            },
            mediaEmbed: {
                previewsInData: true
            }
        },
        editorBody: '',
        url: '/api/news/store',
        method: 'POST',
        selectedSection: 0,
        selectedLanguage: 'en',
        languages: [
            'en', 'rs', 'de', 'fr'
        ],
        video: '',
        thumbnail: '',
        published_on: '{{ date('Y-m-d') }}',
        translations: {},
        images: [],
        uploadedVideo: '',
        uploadedThumbnail: '',
        uploadedImages: {},
        success: false,
        submitError: false
    },
    methods: {
        setImagesAttribute() {
            this.images = event.target.files;
        },
        setVideoAttribute() {
            this.video = event.target.files[0];
        },
        setThumbnailAttribute() {
            this.thumbnail = event.target.files[0];
        },
        setTranslationContent() {
            this.translations[this.selectedLanguage].body = this.editorBody;
            console.log(this.translations);
        },
        selectLanguage(language) {
            this.selectedLanguage = language;
            this.editorBody = this.translations[this.selectedLanguage].body;
        },
        deleteImage(image_id, index) {
            let element = event.target;

            this.$http({
                url: '/api/news/' + this.id + '/images/' + image_id,
                method: 'DELETE',
            })
            .then(() => {
                this.uploadedImages.splice(index, 1);
            });
        },
        sendData() {
            let formData = new FormData();

            if (this.id) {
                formData.append('_method', 'PATCH');
            }

            formData.append('video', this.video);
            formData.append('thumbnail', this.thumbnail);

            formData.append('translations[en][title]', this.translations.en.title);
            formData.append('translations[en][body]', this.translations.en.body);
            formData.append('translations[rs][title]', this.translations.rs.title);
            formData.append('translations[rs][body]', this.translations.rs.body);
            formData.append('translations[de][title]', this.translations.de.title);
            formData.append('translations[de][body]', this.translations.de.body);
            formData.append('translations[fr][title]', this.translations.fr.title);
            formData.append('translations[fr][body]', this.translations.fr.body);

            for (let i = 0; i < this.images.length; i++) {
                formData.append('images['+ i +']', this.images[i]);
            }
            formData.append('published_on', this.published_on);

            this.success = false;
            this.submitError = false;

            this.$http({
                url: this.url,
                method: this.method,
                data: formData
            })
            .then((response) => {
                let data = response.data;

                this.id = data.id;
                this.url = '/api/news/' + this.id;

                this.$refs.video.value = '';
                this.$refs.images.value = '';

                this.uploadedImages = data.images;
                this.uploadedVideo = data.video_src;
                this.uploadedThumbnail = data.thumbnail;

                this.title = 'Editing "'+ data.title +'"';

                this.success = true;
            })
            .catch((error) => {
                this.submitError = true;
            });
        }
    },
    created() {
        for (let i = 0; i < this.languages.length; i++) {
            this.translations[this.languages[i]] = {};
            this.translations[this.languages[i]].title = '';
            this.translations[this.languages[i]].body = '';
        }
    }
});
</script>
@endsection
