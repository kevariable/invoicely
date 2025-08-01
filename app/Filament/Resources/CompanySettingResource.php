<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanySettingResource\Pages;
use App\Filament\Resources\CompanySettingResource\RelationManagers;
use App\Models\CompanySetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanySettingResource extends Resource
{
    protected static ?string $model = CompanySetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $navigationLabel = 'Company Settings';
    
    protected static ?string $modelLabel = 'Company Settings';
    
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Street Address')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('city')
                            ->label('City')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('state')
                            ->label('State/Province')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('zip_code')
                            ->label('ZIP/Postal Code')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('country')
                            ->label('Country')
                            ->required()
                            ->default('United States')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Financial Settings')
                    ->schema([
                        Forms\Components\Select::make('currency')
                            ->label('Default Currency')
                            ->options([
                                'IDR' => 'RP (IDR)',
                                'USD' => '$ US Dollar (USD)',
                                'GBP' => '£ British Pound (GBP)',
                            ])
                            ->required()
                            ->default('USD'),

                        Forms\Components\TextInput::make('tax_rate')
                            ->label('Default Tax Rate (%)')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->default(0),

                        Forms\Components\TextInput::make('tax_id')
                            ->label('Tax ID/Registration Number')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('bank_details')
                            ->label('Bank Details')
                            ->placeholder('Bank name, account number, routing number, etc.')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('city')
                    ->label('City')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('country')
                    ->label('Country')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('tax_rate')
                    ->label('Tax Rate')
                    ->suffix('%')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Settings'),
            ])
            ->bulkActions([
                // Remove bulk actions as we typically only have one company settings record
            ])
            ->emptyStateHeading('No Company Settings Found')
            ->emptyStateDescription('Create your company settings to customize invoices and other documents.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Company Settings'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanySettings::route('/'),
            'create' => Pages\CreateCompanySetting::route('/create'),
            'edit' => Pages\EditCompanySetting::route('/{record}/edit'),
        ];
    }
}
