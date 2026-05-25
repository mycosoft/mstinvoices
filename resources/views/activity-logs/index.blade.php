@extends('adminlte::page')

@section('title', 'Activity Logs')

@section('content_header')
    <h1>Activity Logs</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <div class="row w-100">
                <div class="col-md-2">
                    <select name="user_id" class="form-control form-control-sm w-100">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="model_type" class="form-control form-control-sm w-100">
                        <option value="">All Models</option>
                        @foreach($models as $key => $label)
                            <option value="{{ $key }}" {{ request('model_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="event" class="form-control form-control-sm w-100">
                        <option value="">All Actions</option>
                        @foreach($events as $event)
                            <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>{{ ucfirst($event) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control form-control-sm w-100" value="{{ request('date_from') }}" placeholder="From">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control form-control-sm w-100" value="{{ request('date_to') }}" placeholder="To">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th style="width:140px">Date & Time</th>
                    <th style="width:140px">User</th>
                    <th style="width:100px">Action</th>
                    <th>Model</th>
                    <th>Changes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $activity)
                    <tr>
                        <td class="small">{{ $activity->created_at->format('M d, Y H:i:s') }}</td>
                        <td>
                            @if($activity->causer)
                                {{ $activity->causer->name }}
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $activity->description === 'created' ? 'success' : ($activity->description === 'updated' ? 'warning' : 'danger') }}">
                                {{ $activity->description }}
                            </span>
                        </td>
                        <td>
                            <strong>{{ class_basename($activity->subject_type) }}</strong>
                            @if($activity->subject_id)
                                <small class="text-muted">#{{ $activity->subject_id }}</small>
                            @endif
                            @if($activity->properties->has('attributes') && isset($activity->properties['attributes']['name']))
                                <br><small>{{ $activity->properties['attributes']['name'] ?? '' }}</small>
                            @elseif($activity->properties->has('old') && isset($activity->properties['old']['name']))
                                <br><small>{{ $activity->properties['old']['name'] ?? '' }}</small>
                            @endif
                        </td>
                        <td class="small">
                            @php
                                $attributes = $activity->properties->get('attributes', []);
                                $old = $activity->properties->get('old', []);
                                $changes = [];
                            @endphp
                            @if($activity->description === 'created' && !empty($attributes))
                                @php $changes = array_keys($attributes); @endphp
                                <span class="text-success">Created with: {{ implode(', ', array_slice($changes, 0, 4)) }}</span>
                            @elseif($activity->description === 'updated' && !empty($old))
                                @php $changes = array_intersect_key($attributes, $old); @endphp
                                @foreach(array_slice($changes, 0, 3) as $key => $newValue)
                                    <span class="text-warning">{{ $key }}: {{ Str::limit(json_encode($old[$key] ?? ''), 15) }} → {{ Str::limit(json_encode($newValue), 15) }}</span><br>
                                @endforeach
                            @elseif($activity->description === 'deleted' && !empty($old))
                                @php $changes = array_keys($old); @endphp
                                <span class="text-danger">Deleted: {{ implode(', ', array_slice($changes, 0, 4)) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No activity logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $activities->appends(request()->all())->links() }}
    </div>
</div>
@stop

