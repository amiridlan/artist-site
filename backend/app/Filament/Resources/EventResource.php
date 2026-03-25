<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Actions;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    use HasTranslationFields;

    protected static ?string $model = Event::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Event Info')->schema([
                FormComponents\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                FormComponents\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                FormComponents\Select::make('type')
                    ->options([
                        'concert' => 'Concert',
                        'meet-greet' => 'Meet & Greet',
                        'handshake' => 'Handshake',
                        'online' => 'Online',
                        'other' => 'Other',
                    ])
                    ->required(),
                FormComponents\Select::make('status')
                    ->options([
                        'upcoming' => 'Upcoming',
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                FormComponents\DatePicker::make('date')->required(),
                FormComponents\DatePicker::make('end_date'),
            ])->columns(2),

            Section::make('Location')->schema([
                FormComponents\TextInput::make('venue')->maxLength(255),
                FormComponents\TextInput::make('location')->maxLength(255),
                FormComponents\TextInput::make('ticket_url')
                    ->label('Ticket URL')
                    ->url()
                    ->maxLength(255),
            ])->columns(2),

            Section::make('Details')->schema([
                FormComponents\Textarea::make('description')->rows(4),
                FormComponents\FileUpload::make('image')
                    ->image()
                    ->directory('events'),
            ]),

            static::translationSection([
                'title' => 'text',
                'description' => 'textarea',
                'venue' => 'text',
                'location' => 'text',
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->limit(50),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'concert' => 'success',
                        'meet-greet' => 'info',
                        'handshake' => 'warning',
                        'online' => 'gray',
                        'other' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'upcoming' => 'success',
                        'ongoing' => 'warning',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('venue')->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'concert' => 'Concert',
                        'meet-greet' => 'Meet & Greet',
                        'handshake' => 'Handshake',
                        'online' => 'Online',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'upcoming' => 'Upcoming',
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
