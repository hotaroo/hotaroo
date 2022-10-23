<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport as Export;
use App\Filament\Resources\QuotaResource\Pages;
use App\Models\Quota;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class QuotaResource extends Resource
{
    protected static ?string $model = Quota::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud-download';

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
                    ->label('State of charge [%]')
                    ->sortable(),
                Tables\Columns\TextColumn::make('watts_in_sum')
                    ->label('In [W]')
                    ->sortable(),
                Tables\Columns\TextColumn::make('watts_out_sum')
                    ->label('Out [W]')
                    ->sortable(),
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
                // TODO:
                // Tables\Filters\Filter::make('timestamp')
                //     ->form([
                //         Forms\Components\DateTimePicker::make('from')
                //             ->withoutSeconds(),
                //         Forms\Components\DateTimePicker::make('until')
                //             ->withoutSeconds(),
                //     ])
                //     ->query(function (Builder $query, array $data): Builder {
                //         return $query
                //             ->when(
                //                 $data['from'],
                //                 fn (Builder $query, $datetime): Builder => $query->where('timestamp', '>=', $datetime),
                //             )
                //             ->when(
                //                 $data['until'],
                //                 fn (Builder $query, $datetime): Builder => $query->where('timestamp', '<=', $datetime),
                //             );
                //     })
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Export\Actions\FilamentExportBulkAction::make('export')
                    ->fileName(Str::lower(config('app.name')))
                    ->directDownload(),
            ])
            ->headerActions([
                Export\Actions\FilamentExportHeaderAction::make('export')
                    ->fileName(Str::lower(config('app.name')))
                    ->directDownload(),
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
            'index' => Pages\ListQuotas::route('/'),
            // 'create' => Pages\CreateQuota::route('/create'),
            // 'edit' => Pages\EditQuota::route('/{record}/edit'),
        ];
    }
}
