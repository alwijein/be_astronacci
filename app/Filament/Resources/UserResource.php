<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Users';
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->columns(2)
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Profile Photo')
                            ->image()
                            ->avatar()
                            ->columnSpan(2)
                            ->alignCenter()
                            ->directory('avatars')
                            ->disk('public') // Gunakan disk 'public' untuk menyimpan gambar
                            ->preserveFilenames()
                            ->maxSize(1024) // Maksimal ukuran file 1MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Upload a profile photo. Recommended size: 300x300px.')
                            ->nullable(),

                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->autofocus()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required(),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn(?User $record) => ! $record)
                            ->minLength(8)
                            ->same('passwordConfirmation'),

                        TextInput::make('passwordConfirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->required(fn(?User $record) => ! $record),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url') // Gunakan accessor avatar_url
                    ->label('Avatar')
                    ->circular()
                    ->disk('public') // Disk tempat gambar disimpan
                    ->defaultImageUrl(asset('storage/default_avatar.png')) // Gambar default
                    ->height(50) // Ukuran gambar
                    ->width(50),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Phone'),

                TextColumn::make('email_verified_at')
                    ->label('Verified At')
                    ->dateTime('M d, Y'),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->since(),
            ])
            ->filters([
                Filter::make('verified')
                    ->label('Verified Users')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
