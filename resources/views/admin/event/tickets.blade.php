@extends('_layout.admin.master')
@section('content')
    <div class="table-holder" id="root">
        <table class="table">
            <tbody>
                <tr class="dark-tr" v-show="entries.length < 1">
                    <td colspan="2">There are no entries to show...</td>
                </tr>
                <tr class="dark-tr" v-for="entry in entries">
                    <td> 
                        <h6>Buyer</h6>
                        <h5>@{{ entry.user.first_name }} @{{ entry.user.last_name }}</h5> 
                    </td>
                    <td v-if="entry.used">
                        <h6>Status</h6>
                        <h5>Used</h5>
                    </td>
                    <td v-else>
                        <h6>Status</h6>
                        <h5>Not-used</h5>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        let app = new Vue({
            el: '#root',
            data: {
                id: '{{ $event->id }}',
                entries: {}
            },
            methods: {
                fetchData() {
                    this.$http.get('/api/events/' + this.id + '/tickets')
                    .then((response) => {
                        this.entries = response.data.data;
                    });
                }
            },
            created() {
                this.fetchData();
            }
        });
    </script>
@endsection
