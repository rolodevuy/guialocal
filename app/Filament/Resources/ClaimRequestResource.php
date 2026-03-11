<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClaimRequestResource\Pages;
use App\Mail\ClaimApproved;
use App\Mail\ClaimRejected;
use App\Models\ClaimRequest;
use App\Models\Ficha;
use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ClaimRequestResource extends Resource
{
    protected static ?string $model = ClaimRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Reclamos';

    protected static ?string $modelLabel = 'Reclamo';

    protected static ?string $pluralModelLabel = 'Reclamos';

    protected static ?string $navigationGroup = 'Propietarios';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = ClaimRequest::where('estado', 'pendiente')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lugar.nombre')
                    ->label('Negocio')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Solicitante')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->copyable(),
                Tables\Columns\TextColumn::make('rut_numero')
                    ->label('RUT'),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'aprobado'  => 'success',
                        'rechazado' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ClaimRequest::ESTADOS[$state] ?? $state),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options(ClaimRequest::ESTADOS),
            ])
            ->actions([
                Tables\Actions\Action::make('ver_constancia')
                    ->label('Ver constancia')
                    ->icon('heroicon-o-document')
                    ->color('info')
                    ->url(fn (ClaimRequest $record): ?string => $record->getFirstMediaUrl('constancia_rut') ?: null)
                    ->openUrlInNewTab()
                    ->visible(fn (ClaimRequest $record): bool => $record->hasMedia('constancia_rut')),

                Tables\Actions\Action::make('aprobar')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar reclamo')
                    ->modalDescription(fn (ClaimRequest $record) => "Se creará una cuenta para {$record->nombre_completo} ({$record->email}) y se vinculará al negocio {$record->lugar->nombre}.")
                    ->visible(fn (ClaimRequest $record): bool => $record->estado === 'pendiente')
                    ->action(function (ClaimRequest $record) {
                        // Crear usuario o usar existente
                        $user = User::where('email', $record->email)->first();
                        $isNewUser = false;
                        $password = null;

                        if (!$user) {
                            $password = Str::random(10);
                            $user = User::create([
                                'name'     => $record->nombre_completo,
                                'email'    => $record->email,
                                'password' => Hash::make($password),
                            ]);
                            $isNewUser = true;
                        }

                        // Vincular ficha al usuario y marcar como verificada
                        $ficha = $record->lugar->fichas()->first();
                        if ($ficha) {
                            $ficha->update([
                                'user_id'     => $user->id,
                                'verified_at' => now(),
                            ]);
                        }

                        // Actualizar RUT en el lugar si no tenía
                        if (!$record->lugar->rut) {
                            // Verificar que el RUT no esté en uso por otro lugar
                            $rutEnUso = \App\Models\Lugar::where('rut', $record->rut_numero)
                                ->where('id', '!=', $record->lugar_id)
                                ->exists();

                            if (!$rutEnUso) {
                                $record->lugar->update(['rut' => $record->rut_numero]);
                            }
                        }

                        // Marcar claim como aprobado
                        $record->update([
                            'estado'      => 'aprobado',
                            'admin_id'    => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        // Enviar email según si es usuario nuevo o existente
                        Mail::to($record->email)->send(new ClaimApproved($record, $password, $isNewUser));

                        Notification::make()
                            ->title('Reclamo aprobado')
                            ->body("Se creó cuenta para {$record->nombre_completo} y se vinculó al negocio.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('motivo_rechazo')
                            ->label('Motivo del rechazo')
                            ->required()
                            ->placeholder('Explicá por qué se rechaza la solicitud...')
                            ->rows(3),
                    ])
                    ->visible(fn (ClaimRequest $record): bool => $record->estado === 'pendiente')
                    ->action(function (ClaimRequest $record, array $data) {
                        $record->update([
                            'estado'          => 'rechazado',
                            'motivo_rechazo'  => $data['motivo_rechazo'],
                            'admin_id'        => auth()->id(),
                            'reviewed_at'     => now(),
                        ]);

                        Mail::to($record->email)->send(new ClaimRejected($record));

                        Notification::make()
                            ->title('Reclamo rechazado')
                            ->warning()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClaimRequests::route('/'),
        ];
    }
}
