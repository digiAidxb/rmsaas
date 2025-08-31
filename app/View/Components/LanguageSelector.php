<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\View\Component;

class LanguageSelector extends Component
{
    public string $currentLocale;
    public array $availableLocales;
    public bool $isRTL;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->currentLocale = App::getLocale();
        $this->availableLocales = config('app.available_locales', []);
        $this->isRTL = $this->availableLocales[$this->currentLocale]['rtl'] ?? false;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.language-selector');
    }
}