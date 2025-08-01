<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TextArea extends Component
{
    public string $inputLabel;
    public string $req;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($inputLabel, $req)
    {
        $this->inputLabel = $inputLabel;
        $this->req = $req;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.form.text-area');
    }
}
