<template>
    <div>
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :data="reply" @deleted="remove(index)"></reply>
        </div>

        <paginator :dataSet="dataSet" @updated="fetch"></paginator>

        <p v-if="$parent.closed">
            This thread has been closed, no more replies are allowed
        </p>

        <new-reply @created="add" v-else></new-reply>
    </div>
</template>
<script>
    import Reply from './Reply.vue';
    import NewReply from './NewReply.vue';
    import collection from '../mixins/collection';

    export default {

        components: { Reply, NewReply },

        mixins: [ collection ],

        data(){
            return {
                dataSet: false
            }
        },

        created(){
            let query = location.search.match(/page=(\d+)/);
            let page = query ? query[1] : 1;
            this.fetch(page);
        },

        methods: {
            fetch(page){
                axios.get(this.url(page))
                    .then(this.refresh);
            },

            refresh({data}){
                this.dataSet = data;
                this.items = data.data;

                window.scrollTo(0, 0);
            },

            url(page) {
                return `${location.pathname}/replies?page=${page}`;
            }
        }
    }
</script>