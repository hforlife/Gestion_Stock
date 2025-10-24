<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EditSales extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public Sale $record;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Edit Sale')
                    ->description('Update the sale Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom')
                            ->required(),
                        TextInput::make('email')
                            ->label('Adresse Email')
                            ->email()
                            ->required(),
                        DateTimePicker::make('email_verified_at'),
                        TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->required(),
                        Textarea::make('two_factor_secret')
                            ->columnSpanFull(),
                        Textarea::make('two_factor_recovery_codes')
                            ->columnSpanFull(),
                        DateTimePicker::make('two_factor_confirmed_at'),
                        Select::make('role')
                            ->required()
                            ->default('cashier'),
                        Select::make('customer_id')
                            ->relationship('customer', 'name'),
                        Select::make('payment_method_id')
                            ->relationship('payment_method', 'name'),
                        TextInput::make('total')
                            ->required()
                            ->numeric(),
                        TextInput::make('paid_amount')
                            ->required()
                            ->prefix('FCFA')
                            ->numeric(),
                        TextInput::make('discount')
                            ->required()
                            ->prefix('FCFA')
                            ->numeric()
                            ->default(0.0),
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
            ->title('Sale updated')
            ->success()
            ->body('The sale has been updated successfully.')
            ->send();
    }

    public function render(): View
    {
        return view('livewire.sales.edit-sales');
    }
}
