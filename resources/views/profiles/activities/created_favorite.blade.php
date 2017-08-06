@component('profiles.activities.activity')

    @slot('heading')
        <a href="{{ route('profile', $profileUser->name) }}">
            {{ $profileUser->name }}
        </a>

        favorited a reply
        {{--<a href="{{ $activity->subject->subject->favorited->path() }}">{{ $activity->subject->favorited->thread->title }}</a>--}}

        {{--<span>{{ $activity->subject->created_at->diffForHumans() }}</span>--}}
    @endslot

    @slot('body')
        {{--{{ $activity->subject->favorited->body }}--}}
    @endslot
@endcomponent