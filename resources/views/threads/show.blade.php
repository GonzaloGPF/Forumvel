@extends('layouts.app')

@section('header')
    <link href="{{ asset('vendor/jquery.atwho.css') }}" rel="stylesheet">
@endsection
@section('content')
    <thread-view :thread="{{ $thread }}" inline-template>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    @include('threads.thread')

                    <h3>Replies</h3>

                    <replies @removed="repliesCount--" @created="repliesCount++"></replies>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">

                        <div class="panel-body">
                            <p>
                                This thread was publish {{ $thread->created_at->diffForHumans() }}
                                by <a href="">{{ $thread->creator->name }}</a>
                                and currently has <span v-text="repliesCount"></span> {{ str_plural('comment', $thread->replies_count) }}.
                            </p>
                            <p v-if="isLogged()">
                                <subscribe-button :active="{{ json_encode($thread->isSubscribed) }}"></subscribe-button>
                                <button class="btn btn-default"
                                        v-if="authorize('isAdmin')"
                                        @click="toggleClose()"
                                        v-text="closed ? 'Open' : 'Close'"></button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </thread-view>
@endsection
