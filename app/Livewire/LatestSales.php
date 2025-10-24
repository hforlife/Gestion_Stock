<?php

namespace App\Livewire;

use App\Models\Sale;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestSales extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Sale::query())
            ->columns([
                TextColumn::make('customer.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('saleItems.item.name')
                    ->label('Sold Item')
                    ->bulleted()
                    ->expandableLimitedList()
                    ->limitList(2)
                    ->sortable(),
                TextColumn::make('total')
                    ->money('XOF')
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->money('XOF')
                    ->sortable(),
                TextColumn::make('discount')
                    ->numeric()
                    ->prefix('%')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
