@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                @include('threads.list')

                {{ $threads->render() }}
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Search
                    </div>
                    <div class="panel-body">
                        <form action="/threads/search" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search for..." name="q">
                                <span class="input-group-btn">
                                     <button class="btn btn-default">
                                         <img src="{{asset('images/algolia-mark-blue.png')}}" alt="Algolia" style="width:24px; height:24px">
                                     </button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>

                @if(count($trending))
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Trending Threads
                        </div>
                        <div class="panel-body">
                            <ul class="list-group">
                            @foreach($trending as $thread)
                                <li class="list-group-item">
                                    <a href="{{ $thread->path }}">{{ $thread->title }}</a>
                                </li>
                            @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
