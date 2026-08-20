<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillingReportResource\Pages;
use App\Models\BillingReport;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class BillingReportResource extends Resource
{
    protected static ?string $model = BillingReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Gestión Operativa';

    protected static ?string $modelLabel = 'Reporte de Facturación / Pre-Factura';

    protected static ?string $pluralModelLabel = 'Módulo de Facturación & Cobros';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('folio_factura')
                        ->default(fn () => 'FAC-' . date('Y') . '-' . rand(100, 999))
                        ->required()
                        ->label('Folio de Pre-Factura'),
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'nombre')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->label('Empresa a Facturar'),
                    Forms\Components\Select::make('fixer_id')
                        ->options(fn () => User::where('rol', 'fixer')->get()->mapWithKeys(function ($user) {
                            $tipo = strtoupper($user->tipo_fixer ?? 'n/a');
                            return [$user->id => "{$user->name} [{$tipo} - {$user->especialidad}]"];
                        }))
                        ->required()
                        ->searchable()
                        ->label('Fixer / Contratista Beneficiario'),
                    Forms\Components\Select::make('tipo_fixer')
                        ->options([
                            'interno' => 'Fixer Interno (Nómina/Plantilla)',
                            'externo' => 'Fixer Externo (Proveedor/Contratista)',
                        ])
                        ->required()
                        ->default('externo')
                        ->label('Tipo de Liquidación'),
                    Forms\Components\Select::make('estado')
                        ->options([
                            'borrador' => 'Borrador',
                            'enviado_facturacion' => 'Enviado a Finanzas / Facturación',
                            'aprobado' => 'Aprobado para Pago',
                            'pagado' => 'Pagado / Liquidado',
                        ])
                        ->required()
                        ->default('borrador')
                        ->label('Estado del Pago'),
                    Forms\Components\DateTimePicker::make('fecha_cierre')
                        ->default(now())
                        ->label('Fecha del Paquete de Cobro'),
                    Forms\Components\TextInput::make('total_incidencias')
                        ->numeric()
                        ->default(0)
                        ->readOnly()
                        ->label('Total de Incidencias Agrupadas'),
                    Forms\Components\TextInput::make('monto_total')
                        ->numeric()
                        ->prefix('$')
                        ->default(0.00)
                        ->label('Monto Total Liquidación ($)'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('folio_factura')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->label('Folio Factura'),
                Tables\Columns\TextColumn::make('company.nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Empresa'),
                Tables\Columns\TextColumn::make('fixer.name')
                    ->searchable()
                    ->label('Fixer Beneficiario'),
                Tables\Columns\TextColumn::make('tipo_fixer')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'interno' => 'info',
                        'externo' => 'warning',
                        default => 'secondary',
                    })
                    ->label('Tipo Fixer'),
                Tables\Columns\TextColumn::make('total_incidencias')
                    ->sortable()
                    ->label('Tickets'),
                Tables\Columns\TextColumn::make('monto_total')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->label('Monto Total'),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'borrador' => 'gray',
                        'enviado_facturacion' => 'warning',
                        'aprobado' => 'info',
                        'pagado' => 'success',
                        default => 'secondary',
                    })
                    ->label('Estado Pago'),
                Tables\Columns\TextColumn::make('fecha_cierre')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Fecha Cierre'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'borrador' => 'Borrador',
                        'enviado_facturacion' => 'Enviado a Finanzas',
                        'aprobado' => 'Aprobado',
                        'pagado' => 'Pagado',
                    ]),
                Tables\Filters\SelectFilter::make('tipo_fixer')
                    ->options([
                        'interno' => 'Interno',
                        'externo' => 'Externo',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('recalcular')
                    ->label('Recalcular Totales')
                    ->icon('heroicon-m-calculator')
                    ->color('info')
                    ->action(function (BillingReport $record): void {
                        $record->recalculateTotals();

                        Notification::make()
                            ->title('Totales Recalculados')
                            ->body("Tickets agrupados: {$record->total_incidencias} | Total: \${$record->monto_total}")
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

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return in_array($user->rol, ['admin', 'billing_admin']);
    }

    public static function getRelations(): array
    {
        return [];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBillingReports::route('/'),
            'create' => Pages\CreateBillingReport::route('/create'),
            'edit' => Pages\EditBillingReport::route('/{record}/edit'),
        ];
    }
}
