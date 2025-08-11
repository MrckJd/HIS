<?php

namespace App\Filament\Actions\Table;

use Exception;
use Filament\Tables\Actions\Action;

class IsLeaderAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('is_leader');

        $this->label('Make Leader');

        $this->icon('fas-crown');

        $this->color('warning');

        $this->requiresConfirmation();

        $this->modalHeading('Make Leader?');

        $this->modalDescription('Are you sure you want to make this member the leader?');

        $this->hidden(function($record) {
            return $record->is_leader;
        });

        $this->action(
            function ($record) {
                try{
                    $this->beginDatabaseTransaction();

                    $record->household->members()
                        ->where('id', '!=', $record->id)
                        ->update(['is_leader' => false]);
                    $record->update(['is_leader' => true]);

                    $this->commitDatabaseTransaction();

                    $this->sendSuccessNotification();
                } catch (Exception $e) {
                    $this->rollbackDatabaseTransaction();

                    $this->sendErrorNotification($e);
                }
            });
    }
}
