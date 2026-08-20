<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Estructura Organizacional';

    protected static ?string $modelLabel = 'Sucursal / Edificio';

    protected static ?string $pluralModelLabel = 'Sucursales / Edificios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'nombre')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->label('Empresa / Tenant'),
                    Forms\Components\TextInput::make('nombre')
                        ->required()
                        ->maxLength(255)
                        ->label('Nombre de Sucursal o Edificio'),
                    Forms\Components\TextInput::make('codigo_sucursal')
                        ->maxLength(255)
                        ->label('Código de Sucursal'),
                    Forms\Components\Textarea::make('direccion')
                        ->rows(2)
                        ->columnSpanFull()
                        ->label('Dirección Física'),
                    Forms\Components\TextInput::make('latitud')
                        ->numeric()
                        ->label('Latitud (Geolocalización)'),
                    Forms\Components\TextInput::make('longitud')
                        ->numeric()
                        ->label('Longitud (Geolocalización)'),
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
                    ->label('Sucursal / Edificio'),
                Tables\Columns\TextColumn::make('company.nombre')
                    ->searchable()
                    ->sortable()
                    ->label('Empresa'),
                Tables\Columns\TextColumn::make('codigo_sucursal')
                    ->searchable()
                    ->label('Código'),
                Tables\Columns\TextColumn::make('direccion')
                    ->limit(30)
                    ->label('Dirección'),
                Tables\Columns\TextColumn::make('incidents_count')
                    ->counts('incidents')
                    ->label('Incidencias'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company_id')
                    ->relationship('company', 'nombre')
                    ->label('Filtrar por Empresa'),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
