<template>
    <div>
        <div v-for="(reply, index) in items">
            <reply :data="reply" @deleted="remove(index)"></reply>
        </div>

        <new-reply @created="addReply" :endpoint="endpoint"></new-reply>
    </div>
</template>
<script>
    import Reply from './Reply.vue';
    import NewReply from './NewReply.vue';

    export default {
        props: ['data'],

        components: { Reply, NewReply },

        data(){
            return {
                items: this.data,
                endpoint: location.pathname + '/replies'
            }
        },

        methods: {
            remove(index) {
                console.log('removing index: ' + index  + ' total: '+ this.items.length);
                flash('Your reply has been deleted');
                this.$emit('removed');
            },

            addReply(reply){
                this.items.push(reply);
                this.$emit('created')
            }
        }
    }
</script>