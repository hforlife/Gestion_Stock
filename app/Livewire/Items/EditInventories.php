<?php

namespace App\Livewire\Items;

use App\Models\Inventory;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Schemas\Components\Section;

class EditInventories extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Inventory $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Edit inventory')
                    ->description('Update the Inventory Information')
                    ->columns(2)
                    ->schema([
                        Select::make('item_id')
                            ->label('Item')
                            ->required()
                            ->relationship('item', 'name')
                            ->searchable()
                            ->placeholder('Select an item')
                            ->noSearchResultsMessage('No items found'),
                        TextInput::make('quantity')
                            ->label('Quantité')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);

        Notification::make()
            ->title('Item updated')
            ->success()
            ->body('The item has been updated successfully.')
            ->send();
    }

    public function render(): View
    {
        return view('livewire.items.edit-inventories');
    }
}
