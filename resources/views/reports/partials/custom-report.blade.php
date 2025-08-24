<!-- Custom Report -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-cogs"></i> Custom Report: {{ $report->name }}
            </h3>
            <div class="card-tools">
                <span class="badge badge-info">{{ ucfirst($report->format) }} Format</span>
            </div>
        </div>
        <div class="card-body">
            @if($report->description)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> {{ $report->description }}
                </div>
            @endif
            
            <!-- Custom Report Summary -->
            @if(isset($data['summary']) && is_array($data['summary']))
                <div class="row">
                    @foreach($data['summary'] as $key => $value)
                        <div class="col-md-{{ count($data['summary']) <= 4 ? (12 / count($data['summary'])) : '3' }}">
                            <div class="info-box">
                                <span class="info-box-icon bg-{{ $loop->index % 4 === 0 ? 'primary' : ($loop->index % 4 === 1 ? 'success' : ($loop->index % 4 === 2 ? 'warning' : 'info')) }}">
                                    <i class="fas fa-{{ $loop->index % 6 === 0 ? 'chart-bar' : ($loop->index % 6 === 1 ? 'dollar-sign' : ($loop->index % 6 === 2 ? 'users' : ($loop->index % 6 === 3 ? 'file-invoice' : ($loop->index % 6 === 4 ? 'calculator' : 'chart-line')))) }}"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                    <span class="info-box-number">
                                        @if(is_numeric($value))
                                            @if($value > 1000)
                                                UGX {{ number_format($value, 0) }}
                                            @else
                                                {{ number_format($value, 1) }}
                                            @endif
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@if($report->format === 'chart' && isset($data['chart_data']))
<!-- Custom Chart Display -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-{{ $report->chart_type === 'pie' ? 'pie' : ($report->chart_type === 'line' ? 'line' : 'bar') }}"></i> 
                Data Visualization
            </h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="customChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

@if(($report->format === 'table' || $report->format === 'summary') && isset($data['table_data']))
<!-- Custom Data Table -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table"></i> Detailed Data
            </h3>
            <div class="card-tools">
                <span class="badge badge-secondary">{{ count($data['table_data']) }} Records</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    @if(count($data['table_data']) > 0)
                        <thead>
                            <tr>
                                @foreach(array_keys($data['table_data'][0]) as $column)
                                    <th class="{{ in_array($column, ['amount', 'total', 'revenue', 'value', 'tax', 'payment']) ? 'text-right' : '' }}">
                                        {{ ucwords(str_replace('_', ' ', $column)) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['table_data'] as $row)
                                <tr>
                                    @foreach($row as $column => $value)
                                        <td class="{{ in_array($column, ['amount', 'total', 'revenue', 'value', 'tax', 'payment']) ? 'text-right' : '' }}">
                                            @if(is_numeric($value) && $value > 1000 && in_array($column, ['amount', 'total', 'revenue', 'value', 'tax', 'payment']))
                                                <strong>UGX {{ number_format($value, 0) }}</strong>
                                            @elseif(is_numeric($value) && !in_array($column, ['amount', 'total', 'revenue', 'value', 'tax', 'payment']))
                                                {{ number_format($value, 1) }}
                                            @elseif($value instanceof \Carbon\Carbon)
                                                {{ $value->format('M d, Y') }}
                                            @elseif(is_bool($value))
                                                <span class="badge badge-{{ $value ? 'success' : 'secondary' }}">
                                                    {{ $value ? 'Yes' : 'No' }}
                                                </span>
                                            @elseif(in_array(strtolower($value), ['paid', 'active', 'success', 'completed']))
                                                <span class="badge badge-success">{{ ucfirst($value) }}</span>
                                            @elseif(in_array(strtolower($value), ['pending', 'warning', 'partial']))
                                                <span class="badge badge-warning">{{ ucfirst($value) }}</span>
                                            @elseif(in_array(strtolower($value), ['overdue', 'failed', 'error', 'cancelled']))
                                                <span class="badge badge-danger">{{ ucfirst($value) }}</span>
                                            @elseif(in_array(strtolower($value), ['draft', 'inactive', 'archived']))
                                                <span class="badge badge-secondary">{{ ucfirst($value) }}</span>
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        
                        @if(isset($data['table_totals']) && is_array($data['table_totals']))
                            <tfoot>
                                <tr class="bg-light font-weight-bold">
                                    @foreach(array_keys($data['table_data'][0]) as $column)
                                        <td class="{{ in_array($column, ['amount', 'total', 'revenue', 'value', 'tax', 'payment']) ? 'text-right' : '' }}">
                                            @if(isset($data['table_totals'][$column]))
                                                @if(is_numeric($data['table_totals'][$column]) && $data['table_totals'][$column] > 1000)
                                                    UGX {{ number_format($data['table_totals'][$column], 0) }}
                                                @else
                                                    {{ $data['table_totals'][$column] }}
                                                @endif
                                            @elseif($loop->first)
                                                Total
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        @endif
                    @endif
                </table>
                
                @if(count($data['table_data']) === 0)
                    <div class="text-center py-4">
                        <i class="fas fa-table fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Data Available</h5>
                        <p class="text-muted">No records found matching the report criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Custom Filters and Configuration -->
@if(isset($report->filters) && is_array($report->filters) && count($report->filters) > 0)
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter"></i> Applied Filters
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($report->filters as $filter => $value)
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ ucwords(str_replace('_', ' ', $filter)) }}:</label>
                            <div class="text-muted">
                                @if(is_array($value))
                                    {{ implode(', ', $value) }}
                                @elseif(is_bool($value))
                                    {{ $value ? 'Yes' : 'No' }}
                                @else
                                    {{ $value }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- Custom Analysis and Insights -->
@if(isset($data['insights']) || isset($data['analysis']))
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-lightbulb"></i> Analysis & Insights
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                @if(isset($data['insights']))
                    <div class="col-md-6">
                        <h5>Key Insights</h5>
                        @if(is_array($data['insights']))
                            <ul class="list-unstyled">
                                @foreach($data['insights'] as $insight)
                                    <li>
                                        <i class="fas fa-circle text-{{ $loop->index % 4 === 0 ? 'primary' : ($loop->index % 4 === 1 ? 'success' : ($loop->index % 4 === 2 ? 'warning' : 'info')) }}"></i>
                                        {{ $insight }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>{{ $data['insights'] }}</p>
                        @endif
                    </div>
                @endif
                
                @if(isset($data['analysis']))
                    <div class="col-md-6">
                        <h5>Data Analysis</h5>
                        @if(is_array($data['analysis']))
                            <ul class="list-unstyled">
                                @foreach($data['analysis'] as $analysis)
                                    <li>
                                        <i class="fas fa-chart-line text-{{ $loop->index % 4 === 0 ? 'info' : ($loop->index % 4 === 1 ? 'warning' : ($loop->index % 4 === 2 ? 'success' : 'primary')) }}"></i>
                                        {{ $analysis }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>{{ $data['analysis'] }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Additional Custom Sections -->
@if(isset($data['additional_sections']) && is_array($data['additional_sections']))
    @foreach($data['additional_sections'] as $section)
        <div class="col-{{ $section['width'] ?? '12' }}">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-{{ $section['icon'] ?? 'chart-bar' }}"></i> {{ $section['title'] }}
                    </h3>
                </div>
                <div class="card-body">
                    @if(isset($section['type']) && $section['type'] === 'metrics')
                        <div class="row">
                            @foreach($section['data'] as $metric => $value)
                                <div class="col-md-{{ 12 / count($section['data']) }}">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-{{ $section['color'] ?? 'primary' }}">
                                            <i class="fas fa-{{ $section['metric_icon'] ?? 'chart-bar' }}"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ ucwords(str_replace('_', ' ', $metric)) }}</span>
                                            <span class="info-box-number">
                                                @if(is_numeric($value) && $value > 1000)
                                                    UGX {{ number_format($value, 0) }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {!! $section['content'] ?? 'No content available' !!}
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- Default Message for Empty Custom Reports -->
@if(!isset($data['summary']) && !isset($data['chart_data']) && !isset($data['table_data']))
<div class="col-12">
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Custom Report Configuration</h4>
            <p class="text-muted">This custom report is ready for configuration. Define your data sources, filters, and display preferences to generate meaningful insights.</p>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-table fa-2x text-primary mb-2"></i>
                            <h6>Data Tables</h6>
                            <small class="text-muted">Configure custom data tables with your preferred columns and filters</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                            <h6>Visualizations</h6>
                            <small class="text-muted">Create custom charts and graphs to visualize your data</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-cog fa-2x text-warning mb-2"></i>
                            <h6>Automation</h6>
                            <small class="text-muted">Set up automated generation and distribution schedules</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@section('js')
@parent
<script>
$(document).ready(function() {
    @if($report->format === 'chart' && isset($data['chart_data']))
        initializeCustomChart();
    @endif
});

function initializeCustomChart() {
    const ctx = document.getElementById('customChart');
    if (!ctx) return;
    
    const chartData = @json($data['chart_data']);
    const chartType = '{{ $report->chart_type ?? "bar" }}';
    
    // Prepare chart configuration based on data structure
    let config = {
        type: chartType,
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };
    
    // Customize based on chart type
    if (chartType === 'pie' || chartType === 'doughnut') {
        delete config.options.scales;
        config.options.plugins = {
            legend: {
                position: 'bottom'
            }
        };
    } else if (chartType === 'line') {
        if (config.data.datasets) {
            config.data.datasets.forEach(dataset => {
                dataset.tension = 0.4;
                dataset.fill = false;
            });
        }
    }
    
    // Add currency formatting if dealing with monetary values
    if (chartData.datasets && chartData.datasets.some(dataset => 
        dataset.label && (dataset.label.toLowerCase().includes('revenue') || 
                         dataset.label.toLowerCase().includes('amount') || 
                         dataset.label.toLowerCase().includes('value')))) {
        config.options.scales = config.options.scales || {};
        config.options.scales.y = config.options.scales.y || {};
        config.options.scales.y.ticks = {
            callback: function(value) {
                return 'UGX ' + value.toLocaleString();
            }
        };
        
        config.options.plugins = config.options.plugins || {};
        config.options.plugins.tooltip = {
            callbacks: {
                label: function(context) {
                    return context.dataset.label + ': UGX ' + context.parsed.y.toLocaleString();
                }
            }
        };
    }
    
    new Chart(ctx, config);
}
</script>
@endsection