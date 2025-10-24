<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
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

class ListCustomers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Customer::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Adresse Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('N°Téléphone')
                    ->searchable(),
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
                Action::make('create')
                    ->icon(Heroicon::Plus)
                    ->color('warning')
                    ->url(fn (): string => route('customers.create')),
            ])
            ->recordActions([
                //Modifier
                Action::make('edit')
                    ->icon(Heroicon::PencilSquare)
                    ->color('warning')
                    ->url(fn (Customer $record): string => route('customers.edit', $record)),
                //Supprimer
                Action::make('delete')
                    ->requiresConfirmation()
                    ->icon(Heroicon::Trash)
                    ->color('danger')
                    ->action(fn (Customer $record) => $record->delete())
                    ->successNotification(
                        Notification::make()
                            ->title('Customer has been deleted Successfully')
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
        return view('livewire.customer.list-customers');
    }
}
