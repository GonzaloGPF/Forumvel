<template>
    <div :id="'reply-'+reply.id" class="panel" :class="isBest ? 'panel-success' : 'panel-default'">
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profiles/'+ reply.owner.name"
                       v-text=" reply.owner.name">
                    </a>
                    said <span v-text="ago"></span>...
                </h5>

                <div v-if="isLogged">
                    <favorite :reply="reply"></favorite>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="" v-if="editing">
                <form action="" @submit="update">
                    <div class="form-group">
                        <wysiwyg v-model="body"></wysiwyg>
                    </div>
                    <button class="btn btn-primary">Update</button>
                    <button class="btn btn-link" @click="editing = false" type="button">Cancel</button>
                </form>
            </div>
            <div v-else v-html="body"></div>
        </div>

        <!--@can('update', $reply)-->
        <!--<div class="panel-footer level">-->
        <div class="panel-footer level" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
            <div>
                <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
                <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>
            </div>
            <div>
                <button class="btn btn-xs btn-default ml-a" @click="markAsBest" v-show="authorize('owns', reply.thread)">Best Reply</button>
            </div>
        </div>
        <!--</div>-->
        <!--@endcan-->
    </div>
</template>
<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {

        props: ['data'],

        components: { Favorite },

        data(){
            return {
                editing: false,
                body: this.data.body,
                reply: this.data,
                isBest: this.data.isBest
            }
        },

        computed: {
            ago(){
                return moment(this.reply.created_at).fromNow();
            }
        },

        created(){
            window.events.$on('best-reply-selected', (id) => {
                this.isBest = id === this.reply.id;
            })
        },

        methods: {
            update(){
                axios.patch('/replies/' + this.reply.id, {
                    body: this.body
                }).catch(error => {
                    flash(error.response.data, 'danger');
                });

                this.editing = false;

                flash('Updated!')
            },

            destroy(){
                axios.delete('/replies/' + this.reply.id);

                this.$emit('deleted', this.reply.id);
            },

            markAsBest(){
                axios.post(`/replies/${this.reply.id}/best`);

                window.events.$emit('best-reply-selected', this.reply.id);
            }
        }
    }
</script>