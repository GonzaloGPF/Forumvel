<template>
    <div>
        <div class="level">
            <img :src="avatar" :alt="user.name" width="50" height="50" class="mr-1">
            <h1>
                <span v-text="user.name"></span>
                <small v-text="reputation"></small>
            </h1>
        </div>

        <form v-if="canUpdate" method="post" enctype="multipart/form-data">
            <!-- because image-upload only has one element, attributes will merge (name in this case) -->
            <image-upload name="avatar" class="mr-1" @loaded="onLoad"/>
        </form>

    </div>
</template>
<script>
    import ImageUpload from './ImageUpload.vue';

    export default {
        props: ['user'],

        components: {ImageUpload},

        data() {
            return {
                avatar: this.user.avatar_path
            }
        },

        computed: {
            canUpdate() {
                return this.authorize(user => user.id === this.user.id);
            },
            reputation() {
                return this.user.reputation + ' XP';
            }
        },

        methods: {
            onLoad(avatar) {
                this.avatar = avatar.src;

                // persists to the server
                this.persist(avatar.file);
            },

            persist(avatar) {
                let data = new FormData();

                data.append('avatar', avatar);
                axios.post(`/api/users/${this.user.name}/avatar`, data)
                    .then(() => flash('Avatar uploaded!'));
            }
        }
    }
</script>