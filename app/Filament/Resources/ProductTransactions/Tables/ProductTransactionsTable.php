<?php

namespace App\Filament\Resources\ProductTransactions\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_trx_id')
                    ->label('Booking Trx ID')
                    ->searchable(),

                ImageColumn::make('produk.thumbnail')
                    ->label('Product')
                    ->circular(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),

                

                IconColumn::make('is_paid')
                    ->label('Paid')
                    ->boolean(),

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([

                EditAction::make(),
                DeleteAction::make(),

                Action::make('download_proof')
                    ->label('Download Proof')
                    ->icon('heroicon-o-arrow-down-circle')
                    ->url(
                        fn($record) =>
                        $record->proof
                            ? Storage::url('public/' . $record->proof)
                            : null
                    )
                    ->openUrlInNewTab()
                    ->visible(fn($record) => (bool) $record->proof),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
