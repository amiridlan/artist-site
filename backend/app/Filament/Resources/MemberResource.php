<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Actions;
use Filament\Forms\Components as FormComponents;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MemberResource extends Resource
{
    use HasTranslationFields;

    protected static ?string $model = Member::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static string | \UnitEnum | null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Section::make('Basic Info')->schema([
                FormComponents\TextInput::make('name_english')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                FormComponents\TextInput::make('name_native')
                    ->maxLength(255),
                FormComponents\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                FormComponents\TextInput::make('nickname')
                    ->maxLength(255),
                FormComponents\Select::make('generation')
                    ->options(['1st' => '1st Generation', '2nd' => '2nd Generation'])
                    ->required(),
                FormComponents\Select::make('status')
                    ->options(['active' => 'Active', 'graduated' => 'Graduated', 'concluded' => 'Concluded'])
                    ->default('active')
                    ->required(),
            ])->columns(2),

            Section::make('Profile Details')->schema([
                FormComponents\TextInput::make('birthdate')->maxLength(255),
                FormComponents\TextInput::make('age')->numeric(),
                FormComponents\TextInput::make('height')->numeric()->suffix('cm'),
                FormComponents\TextInput::make('blood_type')->maxLength(10),
                FormComponents\TextInput::make('hometown')->maxLength(255),
                FormComponents\ColorPicker::make('color'),
                FormComponents\TextInput::make('sort_order')->numeric()->default(0),
                FormComponents\DatePicker::make('join_date'),
            ])->columns(2),

            Section::make('Media')->schema([
                FormComponents\FileUpload::make('photo')
                    ->image()
                    ->directory('members')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('3:4')
                    ->imageResizeTargetWidth('400')
                    ->imageResizeTargetHeight('533'),
                FormComponents\FileUpload::make('cover_image')
                    ->image()
                    ->directory('members/covers'),
            ])->columns(2),

            Section::make('Bio & Hobbies')->schema([
                FormComponents\Textarea::make('bio')->rows(4)->columnSpanFull(),
                FormComponents\TagsInput::make('hobbies'),
            ]),

            Section::make('Social Links')->schema([
                FormComponents\KeyValue::make('social')
                    ->keyLabel('Platform')
                    ->valueLabel('URL')
                    ->addActionLabel('Add social link'),
            ]),

            static::translationSection([
                'bio' => 'textarea',
                'hometown' => 'text',
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->defaultImageUrl(fn (Member $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name_english) . '&background=00B4A0&color=fff'),
                Tables\Columns\TextColumn::make('name_english')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name_native')->searchable(),
                Tables\Columns\TextColumn::make('generation')
                    ->badge()
                    ->color(fn (string $state) => $state === '1st' ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'success',
                        'graduated' => 'warning',
                        'concluded' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('generation')
                    ->options(['1st' => '1st Generation', '2nd' => '2nd Generation']),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'graduated' => 'Graduated', 'concluded' => 'Concluded']),
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
