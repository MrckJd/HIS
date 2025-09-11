<?php

namespace App\Filament\Actions\Table;

use App\Enum\RequestActionStatus;
use App\Enum\UserRole;
use Filament\Facades\Filament;
use Filament\Tables\Actions\Action;

class RejectDeleteAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('rejectDeleteMember');

        $this->icon('heroicon-o-check-circle');

        $this->label('Reject Deletion');

        $this->requiresConfirmation();

        $this->modalWidth('md');

        $this->color('danger');

        $this->hidden(fn($record)=> !in_array(Filament::getCurrentPanel()->getId(), [UserRole::ROOT->value, UserRole::ADMIN->value]) || $record->status !== RequestActionStatus::PENDING->value);

        $this->action(function (array $data, $record) {
            try{
                $this->beginDatabaseTransaction();

                $record->create([
                    'action_type' => RequestActionStatus::REJECTED->value,
                    'reason' => 'Rejected deletion request',
                    'status' => RequestActionStatus::REJECTED->value,
                    'member_id' => $record->member->id,
                    'user_id' => request()->user()->id,
                    'meta' => $record->member->toArray(),
                ]);

                $record->member()->restore();

                $this->commitDatabaseTransaction();

                $this->successNotificationTitle('Member deletion request rejected successfully');

                $this->sendSuccessNotification();

            } catch (\Exception $e) {
                $this->rollBackDatabaseTransaction();

                $this->dangerNotificationTitle('Failed to reject deletion request: ' . $e->getMessage());
            }
        });
    }
}
