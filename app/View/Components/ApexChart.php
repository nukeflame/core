<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ApexChart extends Component
{
    public $chartId;
    public $chartType;
    public $chartHeight;
    public $chartWidth;
    public $dataUrl;
    public $chartTitle;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $chartId,
        $chartType = 'line',
        $chartHeight = 200,
        $chartWidth = '100%',
        $dataUrl = null,
        $chartTitle = null
    ) {
        $this->chartId = $chartId;
        $this->chartType = $chartType;
        $this->chartHeight = $chartHeight;
        $this->chartWidth = $chartWidth;
        $this->dataUrl = $dataUrl;
        $this->chartTitle = $chartTitle;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.apex-chart');
    }
}
