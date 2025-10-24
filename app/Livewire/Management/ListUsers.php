<?php

namespace App\Livewire\Management;

use App\Models\User;
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

class ListUsers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => User::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Adresse Email')
                    ->searchable(),
                //TextColumn::make('email_verified_at')
                //    ->dateTime()
                //    ->sortable(),
                // TextColumn::make('two_factor_confirmed_at')
                //    ->dateTime()
                //    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Créer
                Action::make('create')
                    ->icon(Heroicon::Plus)
                    ->color('warning')
                    ->url(fn (): string => route('users.create')),
            ])
            ->recordActions([
                //Modifier
                Action::make('edit')
                    ->icon(Heroicon::PencilSquare)
                    ->color('warning')
                    ->url(fn (User $record): string => route('users.edit', $record)),
                //Supprimer
                Action::make('delete')
                    ->requiresConfirmation()
                    ->icon(Heroicon::Trash)
                    ->color('danger')
                    ->action(fn (User $record) => $record->delete())
                    ->successNotification(
                        Notification::make()
                            ->title('User has been deleted Successfully')
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
        return view('livewire.management.list-users');
    }
}
