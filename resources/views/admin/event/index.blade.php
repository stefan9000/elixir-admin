@extends('_layout.admin.master')
@section('content')
    <div class="table-holder" id="root">
        <a class="cta" href="{{ route('admin_event_create') }}">Create a new event entry</a>
        <table class="table">
            <tbody>
            <tr class="dark-tr">
                <td colspan="5" v-show="entries.length < 1">There are no entries to show...</td>
            </tr>
            <tr class="dark-tr" v-for="entry in entries">
                <!--<td v-html="'<img src=\''+ entry.thumbnail +'\'/>'" v-if="entry.thumbnail"></td>
                <td v-else></td> -->
                <td>
                    <h6>ID</h6>
                    <h5 v-text="entry.id"></h5>
                </td>
                <td >
                    <h6>Name</h6>
                    <h5 v-text="entry.name"></h5>
                </td>
                <td>
                    <h5>Starts on</h5>
                    <h5 v-text="entry.starts_on"></h5>
                </td>
                <td class="action">
                    <a class="ticket" :href="'/_control/event/' + entry.id + '/tickets'" target="_blank">Tickets</a>
                    <a class="view" :href="'/_control/event/' + entry.id">View</a>
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
                    this.$http.get('{{ route('api_event_index') }}', {
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
