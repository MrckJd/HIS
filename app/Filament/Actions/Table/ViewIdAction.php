<?php

namespace App\Filament\Actions\Table;

use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;

class ViewIdAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('viewId');

        $this->label('View ID');

        $this->icon('heroicon-o-eye');

        $this->modalHeading(fn($record) => $record->surname . ' ID Preview');

        $this->closeModalByClickingAway(false);

        $this->modalWidth(MaxWidth::FitContent);

        $this->modalSubmitAction(false);

        $this->modalCancelAction(false);

        $this->hidden(fn()=>!in_array(\Filament\Facades\Filament::getCurrentPanel()->getId(), ['root','admin']));

        $this->modalContent(function ($record) {
            $url = url('id-preview', ['member' => $record->id]);

                 $view = <<<HTML
                    <div style="width:1015px; height:639px; display:flex; align-items:center; justify-content:center;">
                        <iframe src="$url"
                            width="302"
                            height="197"
                            class="border-0 rounded-md m-0 p-0"
                            style="transform: scale(3.36, 3.24);
                                   transform-origin: center;">
                        </iframe>
                    </div>
                HTML;
                return str($view)->toHtmlString();
        });


    }
}
