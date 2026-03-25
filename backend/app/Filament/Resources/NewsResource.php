<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Actions;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    use HasTranslationFields;

    protected static ?string $model = News::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-newspaper';

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make()->schema([
                FormComponents\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                FormComponents\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                FormComponents\Select::make('category')
                    ->options([
                        'news' => 'News',
                        'fanclub' => 'Fanclub',
                        'store' => 'Store',
                        'event' => 'Event',
                        'release' => 'Release',
                    ])
                    ->required(),
                FormComponents\DatePicker::make('date')->required(),
                FormComponents\Toggle::make('featured')->default(false),
                FormComponents\Toggle::make('published')->default(true),
            ])->columns(2),

            Section::make('Content')->schema([
                FormComponents\Textarea::make('excerpt')->required()->rows(3),
                FormComponents\RichEditor::make('content')
                    ->columnSpanFull(),
            ]),

            Section::make('Media')->schema([
                FormComponents\FileUpload::make('image')
                    ->image()
                    ->directory('news'),
            ]),

            static::translationSection([
                'title' => 'text',
                'excerpt' => 'textarea',
                'content' => 'richEditor',
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->limit(50),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'news' => 'info',
                        'event' => 'success',
                        'release' => 'warning',
                        'fanclub' => 'danger',
                        'store' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\IconColumn::make('featured')->boolean(),
                Tables\Columns\IconColumn::make('published')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'news' => 'News',
                        'fanclub' => 'Fanclub',
                        'store' => 'Store',
                        'event' => 'Event',
                        'release' => 'Release',
                    ]),
                Tables\Filters\TernaryFilter::make('featured'),
                Tables\Filters\TernaryFilter::make('published'),
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
