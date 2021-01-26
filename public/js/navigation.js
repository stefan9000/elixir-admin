const nav = new Vue({
    el: '#nav',
    components: {
        navigation: {
            template: `
                    <ul class="main-nav">
                        <li v-for="item in items">
                            <i :class="item.icon"></i>
                            <a :href="item.url" v-html="item.name"></a>
                        </li>
                    </ul>
                `,
            props: ['items']
        }
    },
    data: {
        items: [
            {
                name: 'Dashboard',
                url: '/_control',
                icon: 'fas fa-tachometer-alt',
            },
            {
                name: 'Users List',
                url: '/_control/regular',
                icon: 'fas fa-users',
            },
            {
                name: 'Doormen',
                url: '/_control/doorman',
                icon: 'fas fa-user-shield',
            },
            {
                name: 'Administrators',
                url: '/_control/admin',
                icon: 'fas fa-user-tie',
            },
            {
                name: 'News',
                url: '/_control/news',
                icon: 'fas fa-file',
            },
            {
                name: 'Events',
                url: '/_control/event',
                icon: 'fas fa-calendar-week',
            }
        ]
    }
});
