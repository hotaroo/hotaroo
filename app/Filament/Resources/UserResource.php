<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Account';

    protected static ?string $pluralModelLabel = 'Account';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(
                                fn ($state) => Hash::make($state)
                            )
                            ->dehydrated(fn ($state) => filled($state)),
                    ])
                    ->columnSpan(1),
                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Select::make('timezone')
                            ->options(array_combine(
                                timezone_identifiers_list(),
                                timezone_identifiers_list(),
                            ))
                            ->searchable()
                            ->reactive(),
                        Forms\Components\Placeholder::make('Placeholder')
                            ->label(''),
                        Forms\Components\TextInput::make('date_format')
                            ->placeholder(config('app.date_format'))
                            ->hint(
                                '[Available format characters](https://www.php.net/manual/en/datetime.format.php)'
                            )
                            ->hintIcon('heroicon-s-information-circle')
                            ->helperText(fn ($state, \Closure $get) => now()
                                ->tz($get('timezone'))
                                ->format(
                                    $state ?? config('app.date_format')
                                )
                            )
                            ->reactive(),
                        Forms\Components\TextInput::make('time_format')
                            ->placeholder(config('app.time_format'))
                            ->hint(
                                '[Available format characters](https://www.php.net/manual/en/datetime.format.php)'
                            )
                            ->hintIcon('heroicon-s-information-circle')
                            ->helperText(fn ($state, \Closure $get) => now()
                                ->tz($get('timezone'))
                                ->format(
                                    $state ?? config('app.time_format')
                                )
                            )
                            ->reactive(),
                    ])
                    ->compact()
                    ->columns(2),
                // TODO: validate credentials
                Forms\Components\Section::make('EcoFlow API')
                    ->id('ecoflow')
                    ->schema([
                        Forms\Components\TextInput::make('ecoflow_key')
                            ->label('EcoFlow key'),
                        Forms\Components\TextInput::make('ecoflow_secret')
                            ->label('EcoFlow secret'),
                    ])
                    ->compact(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
