<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public $type;
    public $session;

    public function __construct($type, $session)
    {
        $this->type = $type;
        $this->session = $session;
    }

    public function render()
    {
        return view('components.alert');
    }
}
