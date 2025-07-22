<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('avatar_url')
                    ->label('Gambar Profile')
                    ->directory('avatars')
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull()
                    ->maxSize(1024 * 32),

                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->prefix('+62')
                    ->maxLength(255),


                Forms\Components\TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->required(function ($record) {
                        return !$record;
                    })
                    ->confirmed()
                    ->revealable()
                    ->minLength(6)
                    ->maxLength(255),

                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Konfirmasi Kata Sandi')
                    ->password()
                    ->revealable()
                    ->maxLength(255),
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


    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
