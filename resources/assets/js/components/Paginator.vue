<template>
    <ul class="pagination" v-show="shouldPaginate">
        <li v-show="prevUrl">
            <a href="#" aria-label="Previous" @click.prevent="page--">
                <span aria-hidden="true">&laquo; Previous</span>
            </a>
        </li>
        <li><a href="#">1</a></li>
        <li><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li v-show="nextUrl">
            <a href="#" aria-label="Next" @click.prevent="page++">
                <span aria-hidden="true">Next &raquo;</span>
            </a>
        </li>
    </ul>
</template>
<script>
    export default {
        props: ['dataSet'],

        data(){
            return {
                page: 1,
                prevUrl: false,
                nextUrl: false
            }
        },

        watch: {
            dataSet(){
                this.page = this.dataSet.current_page;
                this.prevUrl = this.dataSet.prev_page_url;
                this.nextUrl = this.dataSet.next_page_url;
            },
            page(){
                this.broadcast().updateUrl();
            }
        },

        computed: {
            shouldPaginate(){
                return !! this.prevUrl || !! this.nextUrl; // casting strings to boolean ^^
            }
        },

        methods: {
            broadcast(){
                return this.$emit('updated', this.page);
            },

            updateUrl(){
                history.pushState(null, null, '?page=' + this.page);
            }
        }
    }
</script>