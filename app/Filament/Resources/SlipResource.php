<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SlipResource\Pages;
use App\Filament\Resources\SlipResource\RelationManagers;
use App\Models\Slip;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SlipResource extends Resource
{
    protected static ?string $model = Slip::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Slip';
    protected static ?int $navigationSort = 2;


    protected static ?string $navigationIcon = 'heroicon-o-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("Informasi Slip")
                    ->schema([
                        Forms\Components\TextInput::make('slip_code')
                            ->required()
                            ->unique(Slip::class, 'slip_code')
                            ->default(function () {
                                return 'SLP-001';
                            })
                            ->maxLength(255)
                            ->label('Kode Slip'),
                        Forms\Components\TextInput::make('plat_number')
                            ->required()
                            ->maxLength(255)
                            ->label('Nomor Plat'),
                        Forms\Components\TextInput::make('driver_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Pemilik Kendaraan'),
                        Forms\Components\TextInput::make('delivery_order')
                            ->required()
                            ->maxLength(255)
                            ->label('Nomor Delivery Order'),
                        Forms\Components\TextInput::make('bruto_muat')
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->label('Bruto Muat'),
                        Forms\Components\TextInput::make('tara_muat')
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->label('Tara Muat'),
                        Forms\Components\TextInput::make('bruto_bongkar')
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->label('Bruto Bongkar'),
                        Forms\Components\TextInput::make('tara_bongkar')
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->label('Tara Bongkar'),
                        Forms\Components\DatePicker::make('date_slip')
                            ->required()
                            ->default(now())
                            ->label('Tanggal Slip'),
                    ])->columns(3),
                Section::make("Relasi Data")
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->required()
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Konsumen'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slip_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('plat_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('driver_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('delivery_order')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bruto_muat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tara_muat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bruto_bongkar')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tara_bongkar')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_slip')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSlips::route('/'),
            'create' => Pages\CreateSlip::route('/create'),
            'edit' => Pages\EditSlip::route('/{record}/edit'),
        ];
    }
}
