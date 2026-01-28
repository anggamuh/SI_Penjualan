<?php

namespace App\Filament\Resources\ProductTransactions\Schemas;

use App\Models\ProductTransaction;
use App\Models\Produk;
use App\Models\ProdukSize;
use App\Models\PromoCode;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;

class ProductTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Wizard::make([
                Step::make('Product & Price')
                    ->schema([

                        Select::make('produk_id')
                            ->label('Shoe')
                            ->relationship('produk', 'name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {

                                $set('produk_size', null);
                                $set('quantity', 1);
                                $set('promo_code_id', null);
                                $set('discount_amount', 0);

                                if ($produk = Produk::find($state)) {
                                    $set('sub_total_amount', $produk->price);
                                    $set('grand_total_amount', $produk->price);
                                }
                            }),

                        Select::make('produk_size')
                            ->label('Shoe Size')
                            ->required()
                            ->options(
                                fn(callable $get) =>
                                $get('produk_id')
                                    ? ProdukSize::where('produk_id', $get('produk_id'))
                                    ->orderBy('size')
                                    ->pluck('size', 'size')
                                    ->toArray()
                                    : []
                            )
                            ->disabled(fn(callable $get) => blank($get('produk_id'))),

                        TextInput::make('quantity')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->live()
                            ->disabled(fn(callable $get) => blank($get('produk_id')))
                            ->maxValue(
                                fn(callable $get) =>
                                Produk::find($get('produk_id'))?->stock ?? 1
                            )
                            ->helperText(
                                fn(callable $get) =>
                                Produk::find($get('produk_id'))
                                    ? 'Stock tersedia: ' . Produk::find($get('produk_id'))->stock
                                    : 'Pilih produk terlebih dahulu'
                            )
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {

                                if ($produk = Produk::find($get('produk_id'))) {
                                    $subTotal = $produk->price * (int) $state;
                                    $discount = $get('discount_amount') ?? 0;

                                    $set('sub_total_amount', $subTotal);
                                    $set('grand_total_amount', max(0, $subTotal - $discount));
                                }
                            }),

                        Select::make('promo_code_id')
                            ->label('Promo Code')
                            ->relationship('promoCode', 'code')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {

                                $subTotal = $get('sub_total_amount') ?? 0;
                                $promo = PromoCode::find($state);

                                $discount = $promo?->discount_amount ?? 0;

                                $set('discount_amount', $discount);
                                $set('grand_total_amount', max(0, $subTotal - $discount));
                            }),

                        TextInput::make('sub_total_amount')
                            ->label('Sub Total Amount')
                            ->prefix('IDR')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('grand_total_amount')
                            ->label('Grand Total Amount')
                            ->prefix('IDR')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('discount_amount')
                            ->label('Discount Amount')
                            ->prefix('IDR')
                            ->numeric()
                            ->disabled(),
                    ])
                    ->columns(2),

                Step::make('Customer Information')
                    ->schema([

                        TextInput::make('name')->required(),

                        TextInput::make('phone')
                            ->tel()
                            ->numeric()
                            ->required()
                            ->maxLength(15),

                        TextInput::make('email')
                            ->email()
                            ->required(),

                        TextInput::make('city')->required(),

                        TextInput::make('post_code')
                            ->required()
                            ->maxLength(10),

                        Textarea::make('address')
                            ->required(),
                    ])
                    ->columns(2),

                Step::make('Payment Information')
                    ->schema([
                        TextInput::make('booking_trx_id')
                            ->default(fn() => (new ProductTransaction())->generateUniqueTrxId())
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        ToggleButtons::make('is_paid')
                            ->label('Payment Status')
                            ->options([
                                1 => 'Paid',
                                0 => 'Unpaid',
                            ])
                            ->icons([
                                1 => 'heroicon-o-check-circle',
                                0 => 'heroicon-o-clock',
                            ])
                            ->colors([
                                1 => 'success',
                                0 => 'warning',
                            ])
                            ->live()
                            ->required(),

                        FileUpload::make('proof')
                            ->disk('public')
                            ->label('Payment Proof')
                            ->directory('ProductTransactions/Proofs')
                            ->acceptedFileTypes([
                                'image/png',
                                'image/jpeg',
                                'application/pdf',
                            ])
                            ->visible(fn($get) => (int) $get('is_paid') === 1)
                            ->required(fn($get) => (int) $get('is_paid') === 1),

                    ]),
            ])
                ->columnSpanFull(),
        ]);
    }
}
