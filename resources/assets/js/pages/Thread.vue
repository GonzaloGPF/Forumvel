<script>
    import Replies from '../components/Replies.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';

    export default {
        props: ['thread'],

        components: { Replies, SubscribeButton },

        data(){
            return {
                repliesCount: this.thread.replies_count,
                closed: this.thread.closed,
                editing: false,
                form: {}
            }
        },

        created() {
            this.resetForm();
        },

        methods: {
            toggleClose(){
                this.closed = ! this.closed;
                let uri = `/closed-threads/${this.thread.slug}`;

                axios[this.closed ? 'delete' : 'post'](uri);
            },

            cancel() {
                this.resetForm();
            },

            update() {
                let uri = `/threads/${this.thread.channel.slug}/${this.thread.slug}`;

                axios.patch(uri, this.form)
                    .then(() => {
                        flash('Your thread have been updated');
                    })
            },
            resetForm() {
                this.form = {
                    title: this.thread.title,
                    body: this.thread.body
                };
                this.editing = false;
            }
        }
    }
</script>