@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="page-header">
                    <h2>
                        {{ $profileUser->name }}
                        <small>{{ $profileUser->created_at->diffForHumans() }}</small>
                    </h2>
                </div>

                @foreach($activities as $date => $activity)
                    <h3 class="page-header">{{ $date }}</h3>
                    @foreach($activity as $record)
                        @include("profiles.activities.{$record->type}", ['activity' => $record])
                    @endforeach
                @endforeach

                {{--{{ $activities->links() }}--}}

            </div>
        </div>


    </div>

@endsection