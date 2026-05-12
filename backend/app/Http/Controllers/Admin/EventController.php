<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SavesTranslations;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    use SavesTranslations;

    private array $translatableFields = ['title', 'description', 'venue', 'location'];

    private function mediaDisk(): string
    {
        return config('filesystems.media_disk');
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Events/Index', [
            'events' => Event::orderByDesc('date')->get([
                'id', 'slug', 'title', 'type', 'status', 'date', 'venue',
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Events/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', 'unique:events,slug'],
            'type'        => ['required', 'in:concert,meet-greet,handshake,online,other'],
            'status'      => ['required', 'in:upcoming,ongoing,completed,cancelled'],
            'date'        => ['required', 'date'],
            'end_date'    => ['nullable', 'date'],
            'venue'       => ['nullable', 'string', 'max:255'],
            'location'    => ['nullable', 'string', 'max:255'],
            'ticket_url'  => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'max:4096'],
        ]);

        $disk = $this->mediaDisk();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', $disk);
        }

        $event = Event::create($data);
        $this->saveTranslations($event, $request->all(), $this->translatableFields);

        return redirect()->route('admin.events.index')->with('success', 'Event created.');
    }

    public function edit(Event $event): Response
    {
        $disk = $this->mediaDisk();

        return Inertia::render('Admin/Events/Edit', [
            'event'        => $event,
            'translations' => $this->loadTranslations($event, $this->translatableFields),
            'imageUrl'     => $event->image ? Storage::disk($disk)->url($event->image) : null,
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', "unique:events,slug,{$event->id}"],
            'type'        => ['required', 'in:concert,meet-greet,handshake,online,other'],
            'status'      => ['required', 'in:upcoming,ongoing,completed,cancelled'],
            'date'        => ['required', 'date'],
            'end_date'    => ['nullable', 'date'],
            'venue'       => ['nullable', 'string', 'max:255'],
            'location'    => ['nullable', 'string', 'max:255'],
            'ticket_url'  => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'max:4096'],
        ]);

        $disk = $this->mediaDisk();

        if ($request->hasFile('image')) {
            if ($event->image) Storage::disk($disk)->delete($event->image);
            $data['image'] = $request->file('image')->store('events', $disk);
        } else {
            unset($data['image']);
        }

        $event->update($data);
        $this->saveTranslations($event, $request->all(), $this->translatableFields);

        return redirect()->route('admin.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $disk = $this->mediaDisk();
        if ($event->image) Storage::disk($disk)->delete($event->image);
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event deleted.');
    }
}
