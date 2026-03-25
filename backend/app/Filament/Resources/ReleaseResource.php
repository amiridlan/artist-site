<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\ReleaseResource\Pages;
use App\Models\Release;
use Filament\Actions;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ReleaseResource extends Resource
{
    use HasTranslationFields;

    protected static ?string $model = Release::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-musical-note';

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Release Info')->schema([
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
                        'single' => 'Single',
                        'album' => 'Album',
                        'ep' => 'EP',
                        'digital-single' => 'Digital Single',
                    ])
                    ->required(),
                FormComponents\DatePicker::make('release_date')->required(),
            ])->columns(2),

            Section::make('Media')->schema([
                FormComponents\FileUpload::make('cover_image')
                    ->image()
                    ->directory('releases'),
                FormComponents\Textarea::make('description')->rows(3),
            ]),

            Section::make('Tracks')->schema([
                FormComponents\Repeater::make('tracks')
                    ->schema([
                        FormComponents\TextInput::make('number')->numeric()->required(),
                        FormComponents\TextInput::make('title')->required(),
                        FormComponents\TextInput::make('duration'),
                    ])
                    ->columns(3)
                    ->defaultItems(1)
                    ->addActionLabel('Add track'),
            ]),

            Section::make('Streaming Links')->schema([
                FormComponents\KeyValue::make('streaming_links')
                    ->keyLabel('Platform')
                    ->valueLabel('URL')
                    ->addActionLabel('Add streaming link'),
            ]),

            static::translationSection([
                'title' => 'text',
                'description' => 'textarea',
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'single' => 'info',
                        'album' => 'success',
                        'ep' => 'warning',
                        'digital-single' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('release_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('release_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'single' => 'Single',
                        'album' => 'Album',
                        'ep' => 'EP',
                        'digital-single' => 'Digital Single',
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
            'index' => Pages\ListReleases::route('/'),
            'create' => Pages\CreateRelease::route('/create'),
            'edit' => Pages\EditRelease::route('/{record}/edit'),
        ];
    }
}
