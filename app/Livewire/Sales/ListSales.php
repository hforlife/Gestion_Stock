<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ListSales extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

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
                //
            ])
            ->headerActions([
                //Créer
                /*Action::make('create')
                    ->icon(Heroicon::Plus)
                    ->color('warning')
                    ->url(fn (): string => route('sales.create')),*/
            ])
            ->recordActions([
                //Modifier
                /*Action::make('edit')
                    ->icon(Heroicon::PencilSquare)
                    ->color('warning')
                    ->url(fn (Sale $record): string => route('sales.edit', $record))
                    ->openUrlInNewTab(),*/
                //Supprimer
                Action::make('delete')
                    ->requiresConfirmation()
                    ->icon(Heroicon::Trash)
                    ->color('danger')
                    ->action(fn (Sale $record) => $record->delete())
                    ->successNotification(
                        Notification::make()
                            ->title('Sales has been deleted Successfully')
                            ->success()
                    )

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.sales.list-sales');
    }
}
