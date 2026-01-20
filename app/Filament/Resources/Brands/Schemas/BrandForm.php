<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                // TextInput::make('slug')
                //     ->required(),
                FileUpload::make('logo')
                    ->image()
                    ->directory('brand/logos')
                    ->maxSize(1024)
                    ->required()
                    ,
            ]);
    }
}
