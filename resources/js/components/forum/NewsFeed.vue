<template>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-12 pb-30 header-text">
				<h1>vACC Germany NOTAM</h1>
				<p>
					The latest notice to airmen from vACC Germany.
				</p>
			</div>
		</div>
		<div class="row">
			<div class="single-blog col-md-12" v-for="n in sortedNews" v-bind:key="n.id">
				<a data-toggle="collapse" :href="'#notam'+n.thread_id" role="button" aria-expanded="false" :aria-controls="'notam'+thread_id">
					<h4>{{ n.title }}</h4>
					<div class="bottom d-flex justify-content-between align-items-center flex-wrap">
						<div>
							<img :src="n.post.User.avatar_urls.m" alt="" height="25">
							<span>{{ n.post.User.username }}</span>
						</div>
						<div class="meta">
							{{ publishedAt(n.post) }}
						</div>
					</div>
				</a>
				<div class="collapse" :id="'notam'+n.thread_id">
					<p class="text-right">
						<a :href="'https://board.vatsim-germany.org/threads/'+n.thread_id" target="_blank">Zum Thread</a>
					</p>
					<p v-html="n.post.parsedMessage"></p>
				</div>
			</div>					
		</div>
	</div>
</template>

<script>
	import moment from 'moment'

	export default{
		data() {
            return {
                news: []
            }
        },
        methods: {
            loadData: function() {
                axios.get('/api/forum/newsfeed').then(res => {
                    this.news = res.data;
                });
            },
            publishedAt: function (post) {

                if(post.last_edit_date > post.post_date){
                    return moment.unix(post.last_edit_date).utc().format('YYYY.MM.DD HH:mm');
                }
                else{
                    return moment.unix(post.post_date).utc().format('YYYY.MM.DD HH:mm');
                }

            },
            checkFirst: function(currentId) {
              return this.news[0].post.post_id === currentId;
            }
        },
        computed: {
        	sortedNews: function() {
        		if(this.news.length >= 1)
        			return _.orderBy(this.news, ['post.last_edit_date', 'post.post_date'], ['desc', 'desc']);
        		else
        			return [];
        	}
        },
        mounted() {
            this.loadData();
        }
	}
</script>