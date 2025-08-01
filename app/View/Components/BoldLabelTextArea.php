<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BoldLabelTextArea extends Component
{
    public string $inputLabel;
    public string $req;
    public string $value;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($inputLabel, $req, $value)
    {
        $this->inputLabel = $inputLabel;
        $this->req = $req;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.form.bold-label-text-area');
    }
}
