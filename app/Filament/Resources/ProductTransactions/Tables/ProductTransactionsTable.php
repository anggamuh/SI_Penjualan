<?php

namespace App\Filament\Resources\ProductTransactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_trx_id')
                    ->label('Transaction ID')
                    ->searchable(),

                IconColumn::make('is_paid')
                    ->label('Paid')
                    ->boolean(),

                TextColumn::make('grand_total_amount')
                    ->label('Grand Total')
                    ->money('idr', true)
                    ->sortable(),

                ImageColumn::make('proof')
                    ->label('Proof')
                    ->circular()
                    ->height(40)
                    ->openUrlInNewTab(),

                TextColumn::make('produk.name')
                    ->label('Product')
                    ->searchable(),

                TextColumn::make('produk_size')
                    ->label('Size')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('sub_total_amount')
                    ->label('Sub Total')
                    ->money('idr', true)
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Customer')
                    ->searchable(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
