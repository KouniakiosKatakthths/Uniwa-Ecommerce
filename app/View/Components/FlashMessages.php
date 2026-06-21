<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FlashMessages extends Component
{
    public array $messages;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->messages = collect([
            'success' => session('success'),
            'warning' => session('warning'),
            'error'   => session('error'),
            'info'    => session('info'),
        ])->filter()->all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.flash-messages');
    }
}
