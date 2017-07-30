@component('profiles.activities.activity')

    @slot('heading')

        <a href="{{ route('profile', $activity->subject->creator) }}">
            {{ $activity->subject->creator->name }}
        </a>
        published a thread:
        <a href="{{ $activity->subject->path() }}">{{ $activity->subject->title }}</a>

        <span>{{ $activity->subject->created_at->diffForHumans() }}</span>
    @endslot

    @slot('body')
        {{ $activity->subject->body }}
    @endslot
@endcomponent