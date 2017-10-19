<script>
    import Replies from '../components/Replies.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';

    export default {
        props: ['thread'],

        components: { Replies, SubscribeButton },

        data(){
            return {
                repliesCount: this.thread.replies_count,
                closed: this.thread.closed
            }
        },

        methods: {
            toggleClose(){
                this.closed = ! this.closed;

                axios[this.closed ? 'delete' : 'post']('/closed-threads/' + this.thread.slug);
            }
        }
    }
</script>