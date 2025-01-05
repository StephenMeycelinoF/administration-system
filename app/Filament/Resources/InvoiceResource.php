<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Slip;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Invoice';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('invoice_number')
                    ->required()
                    ->default(function () {
                        return 'INV-001';
                    })
                    ->maxLength(255),
                Forms\Components\TextInput::make('transport_cost')
                    ->label('Transport Cost')
                    ->numeric()
                    ->required()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $slipId = $get('slip_id');
                        $transportCost = $get('transport_cost');

                        if ($slipId && $transportCost) {
                            $slip = Slip::find($slipId);
                            if ($slip) {
                                $brutoBongkar = $slip->bruto_bongkar;
                                $taraBongkar = $slip->tara_bongkar;

                                // Kalkulasi Total DPP
                                $totalDpp = $transportCost * ($brutoBongkar - $taraBongkar);
                                $set('total_dpp', $totalDpp);

                                // Kalkulasi PPN dan PPH 23
                                $ppn = $totalDpp * 0.11;
                                $set('ppn', $ppn);

                                $pph_23 = $totalDpp * 0.2;
                                $set('pph_23', $pph_23);

                                // Kalkulasi Total Amount
                                $totalAmount = $totalDpp + $ppn - $pph_23;
                                $set('total_amount', $totalAmount);
                            }
                        }
                    }),
                Forms\Components\Select::make('slip_id')
                    ->relationship('slip', 'id')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $slip = Slip::find($state);
                        if ($slip) {
                            $brutoBongkar = $slip->bruto_bongkar;
                            $taraBongkar = $slip->tara_bongkar;
                            $transportCost = $get('transport_cost');

                            // Recalculate total_dpp based on slip_id change
                            $totalDpp = $transportCost * ($brutoBongkar - $taraBongkar);
                            $set('total_dpp', $totalDpp);

                            // Recalculate PPN and PPH 23 when total_dpp is updated
                            $ppn = $totalDpp * 0.11;
                            $set('ppn', $ppn);

                            $pph_23 = $ppn * 0.2;
                            $set('pph_23', $pph_23);

                            $totalAmount = $totalDpp + $ppn - $pph_23;
                            $set('total_amount', $totalAmount);

                            $set('customer_id', $slip->customer_id);
                        }
                    })
                    ->required(),
                Forms\Components\Hidden::make('customer_id')
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $slip = Slip::find($get('slip_id'));
                        if ($slip) {
                            $set('customer_id', $slip->customer_id);
                        }
                    }),
                Forms\Components\TextInput::make('total_dpp')
                    ->label('Total DPP')
                    ->numeric()
                    ->readOnly()
                    ->required(),
                Forms\Components\TextInput::make('ppn')
                    ->label('PPN')
                    ->readOnly()
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('pph_23')
                    ->label('PPH 23')
                    ->readOnly()
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->readOnly()
                    ->numeric(),
                Forms\Components\DatePicker::make('due_date')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('status')
                    ->default('open')
                    ->options([
                        "open" => "Open",
                    ])
                    ->required(),
                Forms\Components\Hidden::make('created_by')
                    ->default(function () {
                        return auth()->id();
                    })
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slip.slip_code')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transport_cost')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_dpp')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ppn')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pph_23')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_by')
                    ->getStateUsing(fn($record) => $record->createdBy?->name)
                    ->label('Dibuat Oleh')
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
