<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Estructura Organizacional';

    protected static ?string $modelLabel = 'Usuario / Perfil';

    protected static ?string $pluralModelLabel = 'Usuarios & Roles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->label('Nombre Completo'),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->label('Correo Electrónico'),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255)
                        ->label('Contraseña'),
                    Forms\Components\Select::make('rol')
                        ->options([
                            'admin' => 'Administrador General',
                            'fm' => 'Facility Manager (FM)',
                            'manager' => 'Gestor / Supervisor',
                            'stakeholder' => 'Stakeholder (Lectura Ejecutiva)',
                            'notifier' => 'Notificador / Reportero',
                            'user' => 'Usuario de Tienda',
                            'fixer' => 'Fixer / Técnico',
                            'billing_admin' => 'Administrador de Facturación',
                        ])
                        ->required()
                        ->reactive()
                        ->label('Rol en el Sistema'),
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'nombre')
                        ->searchable()
                        ->preload()
                        ->label('Empresa / Tenant'),
                    Forms\Components\Select::make('branch_id')
                        ->relationship('branch', 'nombre')
                        ->searchable()
                        ->preload()
                        ->label('Sucursal Principal (Notificador / Tienda)'),
                    Forms\Components\Select::make('branches')
                        ->relationship('branches', 'nombre')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->label('Sucursales Asignadas (FM / Gestor)')
                        ->helperText('Asigna 1 o múltiples sucursales de distintas zonas geográficas.')
                        ->visible(fn (Forms\Get $get): bool => in_array($get('rol'), ['fm', 'manager', 'facility_manager'])),
                    Forms\Components\Select::make('tipo_fixer')
                        ->options([
                            'interno' => 'Fixer Interno (Plantilla)',
                            'externo' => 'Fixer Externo (Contratista/Proveedor)',
                        ])
                        ->label('Tipo de Fixer (Si aplica)'),
                    Forms\Components\TextInput::make('especialidad')
                        ->placeholder('Ej. Electricista, Plomero, HVAC')
                        ->label('Especialidad Técnica'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Email'),
                Tables\Columns\TextColumn::make('rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'fm', 'manager' => 'warning',
                        'stakeholder' => 'info',
                        'notifier', 'user' => 'gray',
                        'fixer' => 'success',
                        default => 'secondary',
                    })
                    ->label('Rol'),
                Tables\Columns\TextColumn::make('branches.nombre')
                    ->label('Sucursales FM')
                    ->badge()
                    ->separator(', '),
                Tables\Columns\TextColumn::make('company.nombre')
                    ->label('Empresa'),
                Tables\Columns\TextColumn::make('branch.nombre')
                    ->label('Sucursal Principal'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rol')
                    ->options([
                        'admin' => 'Administrador',
                        'manager' => 'Gestor',
                        'notifier' => 'Notificador',
                        'fixer' => 'Fixer',
                        'billing_admin' => 'Facturación',
                    ]),
                Tables\Filters\SelectFilter::make('tipo_fixer')
                    ->options([
                        'interno' => 'Interno',
                        'externo' => 'Externo',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('impersonate')
                    ->label('Impersonalizar')
                    ->icon('heroicon-o-user-minus')
                    ->color('warning')
                    ->url(fn (User $record): string => route('impersonate', $record))
                    ->visible(fn (User $record): bool => auth()->check() && (auth()->user()->rol === 'admin' || session()->has('impersonator_id')) && auth()->id() !== $record->id),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return in_array($user->rol, ['admin', 'manager']);
    }


    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
