<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotaSummaryResource\Pages;
use App\Models\QuotaSummary;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class QuotaSummaryResource extends Resource
{
    protected static ?string $model = QuotaSummary::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->devices->isEmpty()) {
            return parent::getEloquentQuery()->whereNull('id');
        } else {
            return parent::getEloquentQuery()->whereBelongsTo(
                auth()->user()->devices
            );
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('timestamp')
                    ->dateTime(
                        auth()->user()->date_format
                        .' '.auth()->user()->time_format
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('device.label'),
                Tables\Columns\TextColumn::make('state_of_charge')
                    ->label('State of charge [%]'),
                Tables\Columns\TextColumn::make('watt_hours_in_sum')
                    ->label('In [Wh]'),
                Tables\Columns\TextColumn::make('watt_hours_in_cumsum')
                    ->label('In cumulative [Wh]'),
                Tables\Columns\TextColumn::make('watt_hours_out_sum')
                    ->label('Out [Wh]'),
                Tables\Columns\TextColumn::make('watt_hours_out_cumsum')
                    ->label('Out cumulative [Wh]'),
            ])
            ->defaultSort('timestamp', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('device')
                    ->relationship(
                        'device',
                        'label',
                        fn (Builder $query) => $query->whereBelongsTo(
                            auth()->user()
                        )
                    ),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListQuotaSummaries::route('/'),
            'create' => Pages\CreateQuotaSummary::route('/create'),
            'edit' => Pages\EditQuotaSummary::route('/{record}/edit'),
        ];
    }
}
