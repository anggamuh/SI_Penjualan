<?php

namespace App\Filament\Resources\Produks\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Information')
                    ->schema([

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('IDR'),

                        FileUpload::make('thumbnail')
                            ->image()
                            ->directory('produk/thumbnails')
                            ->maxSize(1024)
                            ->required(),


                        Repeater::make('photos')
                            ->relationship('photos')
                            ->label('Product Photos')
                            ->schema([
                                FileUpload::make('photo')
                                    ->image()
                                    ->directory('produk/photos')
                                    ->maxSize(1024)
                                    ->required(),
                            ])
                            ->addActionLabel('Add Photo'),


                        Repeater::make('sizes')
                            ->schema([
                                TextInput::make('size')
                                    ->required()
                                    ->numeric(),
                            ])
                            ->addActionLabel('Add to sizes'),


                        Section::make('More Information')
                            ->schema([
                                Textarea::make('about')
                                    ->required(),

                                Select::make('is_popular')
                                    ->label('Is popular')
                                    ->required()
                                    ->options([
                                        '0' => 'No',
                                        '1' => 'Yes',
                                    ]),

                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->required(),

                                Select::make('brand_id')
                                    ->relationship('brand', 'name')
                                    ->required(),

                                TextInput::make('stock')
                                    ->numeric()
                                    ->required()
                                    ->prefix('pcs'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
