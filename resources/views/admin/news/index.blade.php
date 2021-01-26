@extends('_layout.admin.master')
@section('content')
    <div class="table-holder" id="root">
        <a class="cta" href="{{ route('admin_news_create') }}">Create a new news entry</a>
        <table class="table">
            <tbody>
            <tr class="dark-tr">
                <td colspan="4" v-show="entries.length < 1">There are no entries to show...</td>
            </tr>
            <tr class="dark-tr" v-for="entry in entries">
                <!--<td v-html="'<img src=\''+ entry.thumbnail +'\'/>'" v-if="entry.thumbnail"></td>
                <td v-else></td> -->
                <td>
                    <h6>ID</h6>
                    <h5 v-text="entry.id"></h5>
                </td>
                <td>
                    <h5>Title</h5>
                    <h5 v-text="entry.title"></h5>
                </td>
                <td>
                    <h6>Date</h6>
                    <h5 v-text="entry.published_on"></h5>
                </td>
                <td class="action">
                    <a class="view" :href="'/_control/news/' + entry.id">View</a>
                    <button class="delete-btn" type="button" @click="deleteNews(entry.id)">Delete</button>
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
                    this.$http.get('{{ route('api_news_index') }}', {
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
                deleteNews(id) {
                    this.$http.delete('/api/news/' + id)
                    .then(response => {
                        this.fetchData();
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
