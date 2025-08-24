<!-- Custom Report PDF Content -->
<div class="section-title">Custom Report: {{ $report->name }}</div>

@if($report->description)
<div class="insights">
    <h4>Report Description</h4>
    <p>{{ $report->description }}</p>
</div>
@endif

@if(isset($data['summary']) && is_array($data['summary']))
<div class="section-title">Summary Metrics</div>

<div class="summary-grid">
    @php $summaryCount = count($data['summary']); @endphp
    @foreach($data['summary'] as $key => $value)
        @if($loop->index < 8) {{-- Limit to 8 items for PDF layout --}}
        <div class="summary-item" style="width: {{ $summaryCount <= 4 ? 25 : ($summaryCount <= 8 ? 12.5 : 11.11) }}%;">
            <h3>
                @if(is_numeric($value))
                    @if($value > 1000)
                        UGX {{ number_format($value, 0) }}
                    @else
                        {{ number_format($value, 1) }}
                    @endif
                @else
                    {{ $value }}
                @endif
            </h3>
            <p>{{ ucwords(str_replace('_', ' ', $key)) }}</p>
        </div>
        @endif
    @endforeach
</div>
@endif

@if(isset($data['table_data']) && count($data['table_data']) > 0)
<div class="section-title">Detailed Data</div>

<table>
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
        @foreach($data['table_data'] as $index => $row)
            @if($index < 50) {{-- Limit rows for PDF --}}
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
            @endif
        @endforeach
    </tbody>
    
    @if(isset($data['table_totals']) && is_array($data['table_totals']))
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
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
</table>

@if(count($data['table_data']) > 50)
<p><em>Note: Only first 50 records shown in PDF. Use Excel export for complete data.</em></p>
@endif
@endif

@if(isset($report->filters) && is_array($report->filters) && count($report->filters) > 0)
<div class="section-title">Applied Filters</div>

<table>
    <thead>
        <tr>
            <th>Filter</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach($report->filters as $filter => $value)
        <tr>
            <td><strong>{{ ucwords(str_replace('_', ' ', $filter)) }}</strong></td>
            <td>
                @if(is_array($value))
                    {{ implode(', ', $value) }}
                @elseif(is_bool($value))
                    {{ $value ? 'Yes' : 'No' }}
                @else
                    {{ $value }}
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if(isset($data['insights']) || isset($data['analysis']))
<div class="insights">
    <h4>Analysis & Insights</h4>
    
    @if(isset($data['insights']))
        <h5>Key Insights:</h5>
        @if(is_array($data['insights']))
            <ul>
                @foreach($data['insights'] as $insight)
                    <li>{{ $insight }}</li>
                @endforeach
            </ul>
        @else
            <p>{{ $data['insights'] }}</p>
        @endif
    @endif
    
    @if(isset($data['analysis']))
        <h5>Data Analysis:</h5>
        @if(is_array($data['analysis']))
            <ul>
                @foreach($data['analysis'] as $analysis)
                    <li>{{ $analysis }}</li>
                @endforeach
            </ul>
        @else
            <p>{{ $data['analysis'] }}</p>
        @endif
    @endif
</div>
@endif

@if(!isset($data['summary']) && !isset($data['table_data']))
<div class="insights">
    <h4>Custom Report Configuration</h4>
    <p>This custom report is ready for configuration. Define your data sources, filters, and display preferences to generate meaningful insights.</p>
    <ul>
        <li>Configure custom data tables with preferred columns and filters</li>
        <li>Create custom charts and graphs to visualize data</li>
        <li>Set up automated generation and distribution schedules</li>
    </ul>
</div>
@endif