<?php

namespace App\Filament\Resources\Produks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;

class ProduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                ImageColumn::make('thumbnail')
                    ->circular(),
                TextColumn::make('price')
                    ->money('idr', true)
                    ->sortable(),
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('brand.name')
                    ->searchable(),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_popular')
                    ->boolean(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('is_popular')
                    ->label('Popular Shoes')
                    ->query(fn ($query) => $query->where('is_popular', true)),
                Filter::make('category')
                    ->form([
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['category_id'] ?? null,
                            fn ($query, $categoryId) =>
                            $query->where('category_id', $categoryId)
                        );
                    }),
                Filter::make('brand')
                    ->form([
                        Select::make('brand_id')
                            ->label('Brand')
                            ->relationship('brand', 'name'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['brand_id'] ?? null,
                            fn ($query, $brandId) =>
                            $query->where('brand_id', $brandId)
                        );
                    }),
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
