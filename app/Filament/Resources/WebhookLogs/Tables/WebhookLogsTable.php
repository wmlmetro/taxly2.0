<?php

namespace App\Filament\Resources\WebhookLogs\Tables;

use App\Models\WebhookLog;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('irn')->label('IRN')->sortable()->searchable(),
                TextColumn::make('webhook_url')->limit(50)->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                    ]),
                TextColumn::make('status_code')->label('HTTP Code')->sortable(),
                TextColumn::make('sent_at')->dateTime('d M, Y H:i')->label('Sent At'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ])
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('View')
                        ->url(fn(WebhookLog $record): string => route('filament.resources.webhook-logs.edit', $record))
                        ->icon('heroicon-o-eye'),

                    Action::make('Retry')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-refresh')
                        ->color('warning')
                        ->visible(fn(WebhookLog $record): bool => $record->status === 'failed')
                        ->action(function (WebhookLog $record) {
                            try {
                                $response = Http::timeout(15)->post($record->webhook_url, $record->payload);

                                $record->update([
                                    'status' => $response->successful() ? 'success' : 'failed',
                                    'status_code' => $response->status(),
                                    'response_body' => $response->body(),
                                    'error_message' => $response->failed() ? 'Manual retry failed' : null,
                                    'sent_at' => now(),
                                ]);

                                if ($response->successful()) {
                                    Log::info("Webhook manually retried successfully for IRN {$record->irn}");
                                    Notification::make()
                                        ->success()
                                        ->title('Webhook retried successfully.')
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->danger()
                                        ->title('Retry failed. Check response details.')
                                        ->send();
                                }
                            } catch (\Throwable $e) {
                                $record->update([
                                    'status' => 'failed',
                                    'error_message' => $e->getMessage(),
                                ]);
                                Log::error('Manual webhook retry error: ' . $e->getMessage());
                                Notification::make()
                                    ->danger()
                                    ->title('Retry failed: ' . $e->getMessage())
                                    ->send();
                            }
                        }),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
