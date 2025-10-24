<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Schemas\Components\Section;

class EditCustomers extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Customer $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Edit Customer')
                    ->description('Update the customer Information')
                    ->columns(2)
                    ->schema([
                                TextInput::make('name')
                                    ->label('Nom')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Adresse Email')
                                    ->email()
                                    ->required(),
                                TextInput::make('phone')
                                    ->label('N°Telephone')
                                    ->prefix('+223')
                                    ->tel()
                                    ->required(),
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
            ->title('Customer updated')
            ->success()
            ->body('The customer information has been updated successfully.')
            ->send();
    }

    public function render(): View
    {
        return view('livewire.customer.edit-customers');
    }
}
