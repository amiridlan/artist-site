@component('mail::message')
# Hi {{ $name }},

Here's what's new at KLP48 this week:

@if($events->isNotEmpty())
## Upcoming Events
@foreach($events as $event)
- **{{ $event->title }}** — {{ $event->date->format('d M Y') }}@if($event->venue) at {{ $event->venue }}@endif
@endforeach
@endif

@if($releases->isNotEmpty())
## New Releases
@foreach($releases as $release)
- **{{ $release->title }}** — {{ $release->release_date->format('d M Y') }}
@endforeach
@endif

@if($news->isNotEmpty())
## Latest News
@foreach($news as $article)
- **{{ $article->title }}**
@endforeach
@endif

@component('mail::button', ['url' => $frontendUrl])
Visit KLP48
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
