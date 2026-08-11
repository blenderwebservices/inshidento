<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Estructura Organizacional';

    protected static ?string $modelLabel = 'Empresa / Tenant';

    protected static ?string $pluralModelLabel = 'Empresas / Tenants';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('nombre')
                        ->required()
                        ->maxLength(255)
                        ->label('Nombre de la Empresa / Tenant'),
                    Forms\Components\TextInput::make('rfc_tax_id')
                        ->maxLength(255)
                        ->label('RFC / Identificación Fiscal'),
                    Forms\Components\Toggle::make('activo')
                        ->required()
                        ->default(true)
                        ->label('Empresa Activa'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre de Empresa'),
                Tables\Columns\TextColumn::make('rfc_tax_id')
                    ->searchable()
                    ->label('RFC / Tax ID'),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean()
                    ->label('Estado'),
                Tables\Columns\TextColumn::make('branches_count')
                    ->counts('branches')
                    ->label('Sucursales'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Fecha Registro'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
