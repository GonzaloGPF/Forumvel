<template>
    <div>
        <div v-if="isLogged">
            <div class="form-group">
            <textarea class="form-control"
                      name="body"
                      id="body"
                      required
                      placeholder="Have something to say?"
                      rows="5"
                      v-model="body">
            </textarea>
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
                body: ''
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

        computed: {
            isLogged(){
                return window.App.signedIn;
            }
        },

        methods:{
            addReply(){
                axios.post(location.pathname + '/replies', {
                    'body': this.body
                }).catch(error => {

                    flash(error.response.data, 'danger');

                }).then(({data}) => {
                    this.body = '';

                    this.$emit('created', data);
                    flash('Your reply has been sent');
                })
            }
        }
    }
</script>