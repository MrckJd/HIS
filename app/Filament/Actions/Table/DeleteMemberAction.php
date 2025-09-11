<?php

namespace App\Filament\Actions\Table;

use Faker\Core\File;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Tables\Actions\Action;

class DeleteMemberAction extends Action
{
   protected function setUp(): void
   {
        parent::setUp();

        $this->name('deleteMember');

        $this->icon('heroicon-o-trash');

        $this->label('Request Deletion');

        $this->color('danger');

        $this->modalHeading('Request Member Deletion');

        $this->modalDescription('Please provide a reason for deleting this member and optionally upload a supporting document.');

        $this->modalWidth('md');

        $this->slideOver();

        $this->hidden(fn()=> !in_array(\Filament\Facades\Filament::getCurrentPanel()->getId(), ['root','encoder']));

        $this->form([
            MarkdownEditor::make('reason')
                ->label('Reason')
                ->required(),
            FileUpload::make('document')
                ->label('Supporting Document')
                ->helperText('Upload a document to support your deletion request (optional).')
                ->directory('attachments')
                ->nullable()
                ->maxSize(2048)
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']),
        ]);

        $this->action(function (array $data, \App\Models\Member $record) {
            try{
                $this->beginDatabaseTransaction();

                $record->requestActions()->create([
                    'action_type' => 'Delete',
                    'reason' => $data['reason'],
                    'document' => $data['document'] ?? null,
                    'status' => 'Pending',
                    'user_id' => request()->user()->id,
                    'meta' => $record->toArray(),
                ]);

                $record->delete();



                $this->commitDatabaseTransaction();

                $this->successNotificationTitle('Member deletion request submitted successfully');

                $this->sendSuccessNotification();

            }catch(\Exception $e){
                $this->rollbackDatabaseTransaction();

                $this->sendErrorNotification($e);
            }
        });


   }
}
