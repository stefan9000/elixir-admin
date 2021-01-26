@extends('_layout.admin.master')
@section('content')
    <div class="app-container" id="root">
        <div class="container-heading">
            <a class="back" href="{{ route('admin_admin_index') }}">Back to list</a> 
        </div>
        <form @submit.prevent="sendData()">
            <h3>@{{ title }}</h3>
            <label>First name</label>
            <input type="text" v-model="user.first_name" required/>
            <error :errors="validation_errors.first_name"></error>
            <label>Last name</label>
            <input type="text" v-model="user.last_name" required/>
            <error :errors="validation_errors.last_name"></error>
            <label>Email</label>
            <input type="email" v-model="user.email" required/>
            <error :errors="validation_errors.email"></error>
            <label>Phone</label>
            <input type="text" v-model="user.phone"/>
            <error :errors="validation_errors.phone"></error>
            <label>Date of birth</label>
            <input type="text" v-model="user.date_of_birth"/>
            <error :errors="validation_errors.date_of_birth"></error>
            <label>Password</label>
            <input type="password" v-model="user.password"/>
            <error :errors="validation_errors.password"></error>
            <label>Confirm password</label>
            <input type="password" v-model="user.password_confirmation"/>
            <error :errors="validation_errors.password_confirmation"></error>
            <span class="success-msg" v-show="success">Changes saved.</span>
            <span class="warning-msg" v-show="submit_error">Please correct all of the errors before submiting.</span>
            <button class="submit" type="submit">Create Administrator</button>
        </form>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        const app = new Vue({
            el: '#root',
            components: {
                error: {
                    props: ['errors'],
                    template: `
                        <div v-show="errors.length > 0">
                            <span class="warning" v-for="error in errors" v-text="error"></span>
                        </div>
                    `
                }
            },
            data: {
                url: '/api/admins/store',
                method: 'POST',
                success: false,
                submit_error: false,
                validation_errors: {
                    first_name: [],
                    last_name: [],
                    email: [],
                    phone: [],
                    date_of_birth: [],
                    password: [],
                    password_confirmation: []
                },
                user: {
                    id: '',
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    date_of_birth: '',
                    password: '',
                    password_confirmation: ''
                },
            },
            computed: {
                title() {
                    if (this.user.id) {
                        return 'Editing ' + this.user.first_name + ' ' + this.user.last_name;
                    } else {
                        return 'Creating a new administrator';
                    }
                }
            },
            methods: {
                sendData() {
                    this.success = false;
                    this.submit_error = false;

                    this.$http({
                        url: this.url,
                        method: this.method,
                        params: {
                            first_name: this.user.first_name,
                            last_name: this.user.last_name,
                            email: this.user.email,
                            phone: this.user.phone,
                            date_of_birth: this.user.date_of_birth,
                            password: this.user.password,
                            password_confirmation: this.user.password_confirmation
                        }
                    })
                        .then((response) => {
                            let data = response.data;
                            this.success = true;

                            this.user.id = data.id;
                            this.url = '/api/admins/' + data.id;
                            this.method = 'PATCH';
                        })
                        .catch((error) => {
                            let response = error.response;
                            let error_keys = Object.keys(this.validation_errors);

                            this.submit_error = true;

                            for (let i = 0; i < error_keys.length; i++) {
                                if (response.data.errors[error_keys[i]]) {
                                    this.validation_errors[error_keys[i]] = response.data.errors[error_keys[i]];
                                }
                            }
                        });
                }
            }
        });
    </script>
@endsection
