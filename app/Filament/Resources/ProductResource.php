<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product; // Pastikan Model Product di-import
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Set;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationLabel = 'Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Bagian Kiri: Informasi Utama
                Section::make('Informasi Produk')->schema([
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->label('Kategori')
                        ->required()
                        ->searchable()
                        ->preload(),

                    TextInput::make('name')
                        ->label('Nama Produk')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state) {
                            $set('slug', Str::slug($state));
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->readOnly(),

                    TextInput::make('sku')
                        ->label('SKU (Kode Unik)')
                        ->default(fn () => 'SKU-' . strtoupper(Str::random(8)))
                        ->required(),

                    TextInput::make('barcode')
                        ->label('Barcode (Scan)')
                        ->default(fn () => rand(10000000, 99999999))
                        ->required(),
                ])->columns(2),

                // Bagian Kanan/Bawah: Harga & Stok
                Section::make('Harga & Stok')->schema([
                    TextInput::make('cost_price')
                        ->label('Harga Modal')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    TextInput::make('selling_price')
                        ->label('Harga Jual')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    TextInput::make('stock')
                        ->label('Stok Awal')
                        ->numeric()
                        ->default(0)
                        ->required(),
                ])->columns(3),

                // Bagian Upload Gambar
                Section::make('Gambar & Detail')->schema([
                    FileUpload::make('image')
                        ->label('Foto Produk')
                        ->image()
                        ->directory('products')
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Gambar'),
                
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                
                TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->color(fn (string $state): string => $state <= 5 ? 'danger' : 'success'),
                
                TextColumn::make('selling_price')
                    ->money('IDR')
                    ->label('Harga Jual'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                
                // Tombol Custom untuk Print Barcode
                Action::make('print_barcode')
                    ->label('Barcode')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (Product $record) => route('print.barcode', $record))
                    ->openUrlInNewTab(),
                
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}