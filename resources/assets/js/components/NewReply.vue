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
    export default {
        props: ['endpoint'],

        data(){
            return {
                body: '',
            }
        },

        computed: {
            isLogged(){
                return window.App.signedIn;
            }
        },

        methods:{
            addReply(){
                axios.post(this.endpoint, {
                    'body': this.body
                }).then(({data}) => {
                    this.body = '';

                    this.$emit('created', data);
                    flash('Your reply has been sent');
                })
            }
        }
    }
</script>