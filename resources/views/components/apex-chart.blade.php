<div class="card custom-card mb-0 pb-0">
    @if ($chartTitle)
        <div class="card-header">
            <div class="card-title">{{ $chartTitle }}</div>
        </div>
    @endif
    <div class="card-body p-0">
        <div id="{{ $chartId }}" style="height: {{ $chartHeight }}px; width: {{ $chartWidth }};"></div>
    </div>
</div>

@once
    @push('script')
        <script>
            $(document).ready(function() {
                if (typeof initializeCharts === 'function') {
                    initializeCharts();
                }
            });
        </script>
    @endpush
@endonce

@push('script')
    <script>
        function init{{ Str::studly($chartId) }}() {
            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts is not loaded');
                return;
            }
        }

        function renderChart{{ Str::studly($chartId) }}(data) {
            console.log(data)
            var options = {
                chart: {
                    type: '{{ $chartType }}',
                    height: {{ $chartHeight }},
                    width: '{{ $chartWidth }}',
                    toolbar: {
                        show: true
                    }
                },
                series: data.series || [],
                xaxis: {
                    categories: data.categories || []
                },
                colors: ['#008FFB', '#FF4560', '#00E396', '#FEB019', '#775DD0'],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val !== undefined ? val.toLocaleString() : '';
                        }
                    }
                }
            };

            if (data.options) {
                options = $.extend(true, options, data.options);
            }

            if (window.chart{{ Str::studly($chartId) }}) {
                window.chart{{ Str::studly($chartId) }}.destroy();
            }

            window.chart{{ Str::studly($chartId) }} = new ApexCharts(document.querySelector("#{{ $chartId }}"),
                options);
            window.chart{{ Str::studly($chartId) }}.render();
        }

        if (typeof chartInitFunctions === 'undefined') {
            window.chartInitFunctions = [];
        }
        window.chartInitFunctions.push(init{{ Str::studly($chartId) }});

        if (typeof initializeCharts === 'undefined') {
            window.initializeCharts = function() {
                if (window.chartInitFunctions && Array.isArray(window.chartInitFunctions)) {
                    window.chartInitFunctions.forEach(function(initFn) {
                        if (typeof initFn === 'function') {
                            initFn();
                        }
                    });
                }
            };
        }
    </script>
@endpush
