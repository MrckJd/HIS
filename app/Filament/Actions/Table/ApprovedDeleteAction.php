<?php

namespace App\Filament\Actions\Table;

use App\Enum\RequestActionStatus;
use App\Enum\UserRole;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class ApprovedDeleteAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('approvedDeleteMember');

        $this->icon('heroicon-o-check-circle');

        $this->label('Approve Request');

        $this->requiresConfirmation();

        $this->modalWidth('md');

        $this->color('success');

        $this->hidden(fn($record)=> !in_array(Filament::getCurrentPanel()->getId(), [UserRole::ADMIN->value, UserRole::ROOT->value]) || $record->status !== RequestActionStatus::PENDING->value);

        $this->action(function (array $data, $record) {
            try{
                $this->beginDatabaseTransaction();

                $record->create([
                    'action_type' => RequestActionStatus::APPROVED->value,
                    'reason' => 'Approval of deletion request',
                    'status' => RequestActionStatus::APPROVED->value,
                    'meta' => $record->member->toArray(),
                    'user_id' => request()->user()->id,
                ]);

                $record->member()->forceDelete();


                $this->commitDatabaseTransaction();

                $this->successNotificationTitle('Deletion request approved successfully');

                $this->sendSuccessNotification();

            } catch (\Exception $e) {
                $this->rollBackDatabaseTransaction();

                dd($e);

                Notification::make()
                    ->title('Failed to approve deletion request: ' . $e->getMessage())
                    ->danger()
                    ->send();
                // $this->dangerNotificationTitle('Failed to approve deletion request: ' . $e->getMessage());
            }
        });
    }
}
