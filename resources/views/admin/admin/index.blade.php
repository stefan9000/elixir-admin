@extends('_layout.admin.master')
@section('content')
    <div class="table-holder" id="root">
        <a class="cta" href="{{ route('admin_admin_create') }}">Create a new administrator</a>
        <table class="table">

            <tbody>
            <tr class="dark-tr" v-for="entry in entries">
                <td colspan="7" v-show="entries.length < 1">There are no entries to show...</td>
                <td>
                    <h6>ID</h6>
                    <h5 v-text="entry.id"></h5>
                </td>
                <td>
                    <h6>First Name</h6>
                    <h5 v-text="entry.first_name"></h5>
                </td>
                <td>
                    <h6>Last Name</h6>
                    <h5 v-text="entry.last_name"></h5>
                </td>
                <td>
                    <h6>Email</h6>
                    <h5 v-text="entry.email"></h5>
                </td>
                <td>
                    <h6>Phone</h6>
                    <h5 v-text="entry.phone"></h5>
                </td>
                <td>
                    <h6>Date of Birth</h6>
                    <h5 v-text="entry.date_of_birth"></h5>
                </td>
                <td class="action">
                    <a class="view" :href="'/_control/admin/' + entry.id">View</a>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="pagination">
            <span v-for="page in page.total" v-text="page" @click="fetchData(page)" :class="{current: isCurrentPage(page)}"></span>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        const app =new Vue({
            el: '#root',
            data: {
                entries: [],
                page: {
                    current: 0,
                    total: 0
                }
            },
            methods: {
                fetchData(page = 1) {
                    this.$http.get('{{ route('api_admin_index') }}', {
                        params: {
                            page: page
                        }
                    })
                        .then((response) => {
                            let data = response.data;
                            this.entries = data.data;
                            this.page.current = page;
                            this.page.total = data.last_page;
                        });
                },
                isCurrentPage(page) {
                    return (page === this.page.current);
                }
            },
            created() {
                this.fetchData();
            }
        });
    </script>
@endsection
