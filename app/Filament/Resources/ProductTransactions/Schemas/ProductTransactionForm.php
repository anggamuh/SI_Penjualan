<?php

namespace App\Filament\Resources\ProductTransactions\Schemas;

use App\Models\ProductTransaction;
use App\Models\Produk;
use App\Models\PromoCode;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class ProductTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Transaction Information')
                ->columnSpanFull()
                ->columns(2)
                ->components([
                    TextInput::make('booking_trx_id')
                        ->label('Transaction ID')
                        ->default(fn () => (new ProductTransaction())->generateUniqueTrxId())
                        ->disabled()
                        ->dehydrated()
                        ->required(),

                    Select::make('is_paid')
                        ->label('Payment Status')
                        ->required()
                        ->options([
                            0 => 'Not Paid',
                            1 => 'Paid',
                        ]),

                    TextInput::make('name')
                        ->required(),

                    TextInput::make('phone')
                        ->tel()
                        ->required()
                        ->maxLength(15),

                    TextInput::make('email')
                        ->email()
                        ->required(),

                    TextInput::make('city')
                        ->required(),

                    TextInput::make('post_code')
                        ->required()
                        ->maxLength(10),

                    Textarea::make('address')
                        ->required()
                        ->columnSpanFull(),
                ]),

            Section::make('Product Detail')
                ->columnSpanFull()
                ->columns(2)
                ->components([
                    Select::make('produk_id')
                        ->label('Product')
                        ->relationship('produk', 'name')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $get, $set) {
                            $produk = Produk::find($state);
                            $qty = $get('quantity') ?? 1;

                            if ($produk) {
                                $subTotal = $produk->price * $qty;
                                $set('sub_total_amount', $subTotal);
                                $set('grand_total_amount', $subTotal);
                            }
                        }),

                    TextInput::make('produk_size')
                        ->numeric()
                        ->required(),

                    TextInput::make('quantity')
                        ->numeric()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $get, $set) {
                            $produk = Produk::find($get('produk_id'));

                            if ($produk) {
                                $subTotal = $produk->price * $state;
                                $set('sub_total_amount', $subTotal);
                                $set('grand_total_amount', $subTotal);
                            }
                        }),

                    Select::make('promo_code_id')
                        ->label('Promo Code')
                        ->relationship('promoCode', 'code')
                        ->reactive()
                        ->afterStateUpdated(function ($state, $get, $set) {
                            $subTotal = $get('sub_total_amount') ?? 0;
                            $promo = PromoCode::find($state);

                            $set(
                                'grand_total_amount',
                                $promo
                                    ? max(0, $subTotal - $promo->discount_amount)
                                    : $subTotal
                            );
                        }),

                    TextInput::make('sub_total_amount')
                        ->label('Sub Total')
                        ->numeric()
                        ->prefix('IDR')
                        ->disabled()
                        ->dehydrated(),

                    TextInput::make('grand_total_amount')
                        ->label('Grand Total')
                        ->numeric()
                        ->prefix('IDR')
                        ->disabled()
                        ->dehydrated(),
                ]),
            Section::make('Payment Proof')
                ->columnSpanFull()
                ->components([
                    FileUpload::make('proof')
                        ->directory('ProductTransactions/Proofs')
                        ->acceptedFileTypes([
                            'image/png',
                            'image/jpeg',
                            'application/pdf',
                        ])
                        ->required(),
                ]),
        ]);
    }
}
