<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PageHeader extends Component
{
    public string $title;
    public ?string $subtitle;
    public ?string $badge;

    public function __construct(string $title = '', ?string $subtitle = null, ?string $badge = null)
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->badge = $badge;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.page-header');
    }
}
