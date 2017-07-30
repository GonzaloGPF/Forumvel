@component('profiles.activities.activity')

    @slot('heading')
        <a href="{{ route('profile', $activity->subject->owner->creator) }}">
            {{ $activity->subject->owner->name }}
        </a>
        replied to:
        <a href="{{ $activity->subject->thread->path() }}">{{ $activity->subject->thread->title }}</a>

        <span>{{ $activity->subject->created_at->diffForHumans() }}</span>
    @endslot

    @slot('body')
        {{ $activity->subject->body }}
    @endslot
@endcomponent