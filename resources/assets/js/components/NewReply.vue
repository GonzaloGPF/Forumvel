<template>
    <div>
        <div v-if="isLogged">
            <div class="form-group">
                <wysiwyg name="body"
                         v-model="body"
                         placeholder="Have something to say?"
                         :shouldClear="completed"></wysiwyg>
            </div>

            <button class="btn btn-default" @click="addReply">Post</button>
        </div>
        <p class="text-center" v-else>
            Please <a href="/login">login</a> to participate in discussion
        </p>

    </div>
</template>
<script>
    import 'jquery.caret'
    import 'at.js';

    export default {

        data(){
            return {
                body: '',
                completed: false
            }
        },

        mounted(){
            $('#body').atwho({
                at: '@',
                delay: 750,
                callbacks: {
                    remoteFilter(query, callback){
                        $.getJSON('/api/users', {name: query}, function(usernames) {
                            callback(usernames);
                        })
                    }
                }
            })
        },

        methods:{
            addReply(){
                axios.post(location.pathname + '/replies', {
                    'body': this.body
                }).catch(error => {

                    flash(error.response.data, 'danger');

                }).then(({data}) => {
                    this.body = '';
                    // After Reply is added, we would like to clean up the wysiwyg form.
                    // We could use this.$refs.trix.$refs.trix.value = '';
                    // Or we could make child listen to parent with this.$parent.on('event', callback)
                    // But instead, we will use a data driven approach using a 'completed' property
                    this.completed = true;
                    this.$emit('created', data);
                    flash('Your reply has been sent');
                })
            }
        }
    }
</script>