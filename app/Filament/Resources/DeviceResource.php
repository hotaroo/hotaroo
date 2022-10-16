<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Device;
use Brick\Money\ISOCurrencyProvider;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    protected static ?string $navigationIcon = 'heroicon-o-lightning-bolt';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereBelongsTo(auth()->user());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('serial_number')
                    ->unique(ignoreRecord: true)
                    ->required(),
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('latitude')
                    ->required()
                    ->mask(fn (Forms\Components\TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(5)
                        ->minValue(-90)
                        ->maxValue(90)
                        ->padFractionalZeros()
                    ),
                Forms\Components\TextInput::make('longitude')
                    ->required()
                    ->mask(fn (Forms\Components\TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(5)
                        ->minValue(-180)
                        ->maxValue(180)
                        ->padFractionalZeros()
                    ),
                Forms\Components\Select::make('currency')
                    ->options(
                        array_map(
                            fn ($currency) => $currency->getCurrencyCode(),
                            ISOCurrencyProvider::getInstance()
                                ->getAvailableCurrencies()
                        )
                    )
                    ->searchable()
                    ->reactive(),
                Forms\Components\TextInput::make('investment')
                    ->prefix(fn (\Closure $get) => $get('currency'))
                    ->mask(fn (Forms\Components\TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(2)
                        ->minValue(0)
                        ->maxValue(999_999)
                        ->padFractionalZeros()
                        ->thousandsSeparator('\'')
                    ),
                Forms\Components\TextInput::make('price_per_kilowatt_hour')
                    ->prefix(fn (\Closure $get) => $get('currency'))
                    ->mask(fn (Forms\Components\TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(4)
                        ->minValue(0)
                        ->maxValue(9.9999)
                        ->padFractionalZeros()
                        ->thousandsSeparator('\'')
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serial_number'),
                Tables\Columns\TextInputColumn::make('name'),
                Tables\Columns\TextColumn::make('latitude'),
                Tables\Columns\TextColumn::make('longitude'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('In charge since')
                    ->dateTime(auth()->user()->date_format),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }
}
