<?php

namespace App\Filament\Actions\Table;

use App\Enum\RequestActionStatus;
use Filament\Facades\Filament;
use Filament\Tables\Actions\Action;

class CancelDeleteMemberAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('cancelDeleteMember');

        $this->icon('heroicon-o-x-circle');

        $this->label('Cancel Request');

        $this->requiresConfirmation();

        $this->modalWidth('md');

        $this->color('gray');

        $this->hidden(fn($record)=> !in_array(Filament::getCurrentPanel()->getId(), ['root','encoder']) || $record->status == RequestActionStatus::CANCELLED->value);


        $this->action(function (array $data, $record) {
            try{
                $this->beginDatabaseTransaction();

                $record->create([
                    'action_type' => RequestActionStatus::CANCELLED->value,
                    'reason' => 'Cancellation of deletion request',
                    'status' => RequestActionStatus::CANCELLED->value,
                    'member_id' => $record->member->id,
                    'user_id' => request()->user()->id,
                    'meta' => $record->member->toArray(),
                ]);

                $record->member()->restore();


                $this->commitDatabaseTransaction();

                $this->successNotificationTitle('Deletion request cancelled successfully');

                $this->sendSuccessNotification();

            } catch (\Exception $e) {
                $this->rollBackDatabaseTransaction();
                $this->dangerNotificationTitle('Failed to cancel deletion request: ' . $e->getMessage());
            }
        });
    }
}
