<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Actions;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VideoResource extends Resource
{
    use HasTranslationFields;

    protected static ?string $model = Video::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-video-camera';

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Video Info')->schema([
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
                        'music-video' => 'Music Video',
                        'performance' => 'Performance',
                        'dance-practice' => 'Dance Practice',
                        'behind-the-scenes' => 'Behind the Scenes',
                    ])
                    ->required(),
                FormComponents\DatePicker::make('date')->required(),
                FormComponents\TextInput::make('youtube_id')
                    ->label('YouTube ID')
                    ->maxLength(255),
                FormComponents\TextInput::make('duration')->maxLength(50),
                FormComponents\TextInput::make('venue')->maxLength(255),
            ])->columns(2),

            Section::make('Media')->schema([
                FormComponents\FileUpload::make('thumbnail')
                    ->image()
                    ->directory('videos'),
                FormComponents\Textarea::make('description')->rows(3),
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
                Tables\Columns\ImageColumn::make('thumbnail'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->limit(50),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'music-video' => 'success',
                        'performance' => 'info',
                        'dance-practice' => 'warning',
                        'behind-the-scenes' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('youtube_id')->label('YouTube ID')->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'music-video' => 'Music Video',
                        'performance' => 'Performance',
                        'dance-practice' => 'Dance Practice',
                        'behind-the-scenes' => 'Behind the Scenes',
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
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
