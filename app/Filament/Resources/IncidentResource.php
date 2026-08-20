<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncidentResource\Pages;
use App\Models\Incident;
use App\Models\User;
use App\Models\BillingReport;
use App\Models\IncidentLog;
use App\Models\IncidentMedia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Gestión Operativa';

    protected static ?string $modelLabel = 'Incidencia / Ticket';

    protected static ?string $pluralModelLabel = 'Incidencias & Tickets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ubicación & Origen de la Incidencia')
                    ->schema([
                        Forms\Components\TextInput::make('codigo_ticket')
                            ->default(fn () => Incident::generateTicketCode())
                            ->required()
                            ->readOnly()
                            ->label('Código de Ticket'),
                        Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Sucursal / Edificio'),
                        Forms\Components\Select::make('notifier_id')
                            ->relationship('notifier', 'name', fn (Builder $query) => $query->where('rol', 'notifier')->orWhere('rol', 'admin'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Notificador (Reportero)'),
                        Forms\Components\TextInput::make('ubicacion_especifica')
                            ->placeholder('Ej. Piso 3, Cuarto de Máquinas')
                            ->label('Ubicación Específica en Sucursal'),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles Técnicos de la Falla')
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->required()
                            ->maxLength(255)
                            ->label('Título de la Falla'),
                        Forms\Components\Select::make('categoria_id')
                            ->relationship('category', 'nombre')
                            ->required()
                            ->label('Categoría de Falla'),
                        Forms\Components\Select::make('prioridad')
                            ->options([
                                'baja' => 'Baja',
                                'media' => 'Media',
                                'alta' => 'Alta',
                                'critica' => 'Crítica',
                            ])
                            ->required()
                            ->default('media')
                            ->label('Nivel de Prioridad'),
                        Forms\Components\Select::make('estado')
                            ->options([
                                'abierta' => 'Abierta (Pendiente de Triaje)',
                                'asignada' => 'Asignada a Fixer',
                                'en_progreso' => 'En Progreso (Trabajo de Campo)',
                                'resuelta' => 'Resuelta / Cerrada',
                                'cancelada' => 'Cancelada',
                            ])
                            ->required()
                            ->default('abierta')
                            ->label('Estado Operativo'),
                        Forms\Components\Textarea::make('descripcion')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull()
                            ->label('Descripción Detallada de la Falla'),
                    ])->columns(2),

                Forms\Components\Section::make('Asignación de Fixer (Interno / Externo)')
                    ->schema([
                        Forms\Components\Select::make('manager_id')
                            ->relationship('manager', 'name', fn (Builder $query) => $query->whereIn('rol', ['manager', 'admin']))
                            ->searchable()
                            ->label('Gestor / Supervisor Responsable'),
                        Forms\Components\Select::make('fixer_id')
                            ->options(fn () => User::where('rol', 'fixer')->get()->mapWithKeys(function ($user) {
                                $tipo = strtoupper($user->tipo_fixer ?? 'n/a');
                                return [$user->id => "{$user->name} [{$tipo} - {$user->especialidad}]"];
                            }))
                            ->searchable()
                            ->label('Fixer Asignado'),
                    ])->columns(2),

                Forms\Components\Section::make('Módulo de Liquidación & Costos')
                    ->schema([
                        Forms\Components\TextInput::make('costo_mano_obra')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00)
                            ->label('Costo Mano de Obra'),
                        Forms\Components\TextInput::make('costo_materiales')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00)
                            ->label('Costo Materiales & Refacciones'),
                        Forms\Components\Select::make('billing_report_id')
                            ->relationship('billingReport', 'folio_factura')
                            ->searchable()
                            ->preload()
                            ->label('Lote de Facturación Asignado'),
                        Forms\Components\DateTimePicker::make('fecha_resolucion')
                            ->label('Fecha / Hora de Cierre'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo_ticket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->label('Código Ticket'),
                Tables\Columns\TextColumn::make('branch.nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Sucursal'),
                Tables\Columns\TextColumn::make('titulo')
                    ->searchable()
                    ->limit(30)
                    ->label('Título / Falla'),
                Tables\Columns\TextColumn::make('category.nombre')
                    ->badge()
                    ->color('gray')
                    ->label('Categoría'),
                Tables\Columns\TextColumn::make('prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baja' => 'gray',
                        'media' => 'info',
                        'alta' => 'warning',
                        'critica' => 'danger',
                        default => 'secondary',
                    })
                    ->label('Prioridad'),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'abierta' => 'warning',
                        'asignada' => 'info',
                        'en_progreso' => 'primary',
                        'resuelta' => 'success',
                        'cancelada' => 'danger',
                        default => 'secondary',
                    })
                    ->label('Estado'),
                Tables\Columns\TextColumn::make('fixer.name')
                    ->placeholder('Sin Asignar')
                    ->label('Fixer Asignado'),
                Tables\Columns\TextColumn::make('costo_total')
                    ->state(fn (Incident $record) => '$' . number_format($record->costo_mano_obra + $record->costo_materiales, 2))
                    ->label('Costo Total'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Creado'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'abierta' => 'Abierta',
                        'asignada' => 'Asignada',
                        'en_progreso' => 'En Progreso',
                        'resuelta' => 'Resuelta',
                        'cancelada' => 'Cancelada',
                    ]),
                Tables\Filters\SelectFilter::make('prioridad')
                    ->options([
                        'baja' => 'Baja',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'critica' => 'Crítica',
                    ]),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->relationship('branch', 'nombre')
                    ->label('Sucursal'),
            ])
            ->actions([
                // Acciones personalizadas del flujo operativo
                Tables\Actions\Action::make('asignarFixer')
                    ->label('Asignar Fixer')
                    ->icon('heroicon-m-user-plus')
                    ->color('info')
                    ->visible(fn (Incident $record) => in_array($record->estado, ['abierta', 'asignada']))
                    ->form([
                        Forms\Components\Select::make('fixer_id')
                            ->options(fn () => User::where('rol', 'fixer')->get()->mapWithKeys(function ($user) {
                                $tipo = strtoupper($user->tipo_fixer ?? 'n/a');
                                return [$user->id => "{$user->name} [{$tipo} - {$user->especialidad}]"];
                            }))
                            ->required()
                            ->label('Seleccionar Fixer (Interno / Externo)'),
                        Forms\Components\Textarea::make('comentario')
                            ->placeholder('Indicaciones para el técnico')
                            ->label('Instrucción al Fixer'),
                    ])
                    ->action(function (Incident $record, array $data): void {
                        $oldStatus = $record->estado;
                        $record->fixer_id = $data['fixer_id'];
                        $record->manager_id = auth()->id();
                        $record->estado = 'asignada';
                        $record->save();

                        IncidentLog::create([
                            'incident_id' => $record->id,
                            'estado_anterior' => $oldStatus,
                            'estado_nuevo' => 'asignada',
                            'usuario_id' => auth()->id(),
                            'comentario' => 'Fixer asignado: ' . ($data['comentario'] ?? 'Sin comentario'),
                        ]);

                        Notification::make()
                            ->title('Incidencia Asignada con Éxito')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('cerrarIncidencia')
                    ->label('Resolver & Evidencia')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->visible(fn (Incident $record) => in_array($record->estado, ['asignada', 'en_progreso']))
                    ->form([
                        Forms\Components\TextInput::make('costo_mano_obra')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00)
                            ->required()
                            ->label('Costo Mano de Obra ($)'),
                        Forms\Components\TextInput::make('costo_materiales')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00)
                            ->required()
                            ->label('Costo Materiales / Refacciones ($)'),
                        Forms\Components\Textarea::make('comentario')
                            ->required()
                            ->label('Evidencia / Reporte Técnico de Cierre'),
                    ])
                    ->action(function (Incident $record, array $data): void {
                        $oldStatus = $record->estado;
                        $record->costo_mano_obra = $data['costo_mano_obra'];
                        $record->costo_materiales = $data['costo_materiales'];
                        $record->estado = 'resuelta';
                        $record->fecha_resolucion = now();
                        $record->save();

                        IncidentLog::create([
                            'incident_id' => $record->id,
                            'estado_anterior' => $oldStatus,
                            'estado_nuevo' => 'resuelta',
                            'usuario_id' => auth()->id(),
                            'comentario' => 'Cierre técnico: ' . $data['comentario'],
                        ]);

                        Notification::make()
                            ->title('Incidencia Resuelta')
                            ->body('Se registraron los costos y la evidencia de solución.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (!$user) {
            return $query;
        }

        if ($user->rol === 'admin') {
            return $query;
        }

        if ($user->rol === 'manager') {
            return $query;
        }

        if ($user->rol === 'fixer') {
            return $query->where(function ($q) use ($user) {
                $q->where('fixer_id', $user->id)
                  ->orWhereNull('fixer_id');
            });
        }

        if ($user->rol === 'notifier') {
            return $query->where('notifier_id', $user->id);
        }

        if ($user->rol === 'billing_admin') {
            return $query->where('estado', 'resuelta');
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncidents::route('/'),
            'create' => Pages\CreateIncident::route('/create'),
            'edit' => Pages\EditIncident::route('/{record}/edit'),
        ];
    }
}
