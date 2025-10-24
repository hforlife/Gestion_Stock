<?php

namespace App\Livewire\Management;

use App\Models\User;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateUsers extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Create User')
                    ->description('Creation user Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom')
                            ->placeholder('John Doe')
                            ->required(),
                        TextInput::make('email')
                            ->label('Adresse Email')
                            ->placeholder('')
                            ->email()
                            ->required(),
                        // DateTimePicker::make('email_verified_at'),
                        Select::make('role')
                            ->required()
                            ->options([
                                'Admin' => 'Admin',
                                'User' => 'User',
                                'Moderator' => 'Moderator',
                            ])
                            ->native(false),
                        // Textarea::make('two_factor_secret')
                        //    ->columnSpanFull(),
                        // Textarea::make('two_factor_recovery_codes')
                        //    ->columnSpanFull(),
                        // DateTimePicker::make('two_factor_confirmed_at'),
                        TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->required(),
                    ])
            ])
            ->statePath('data')
            ->model(User::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = User::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('User Created!')
            ->success()
            ->body('The user has been created successfully.')
            ->send();
    }

    public function render(): View
    {
        return view('livewire.management.create-users');
    }
}
