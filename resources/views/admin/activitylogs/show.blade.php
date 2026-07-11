@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Activity Detail</h4>
    <div class="card p-3">
        <p><strong>Date:</strong> {{ $activity->created_at->format('d/m/Y H:i:s') }}</p>
        <p><strong>User:</strong> {{ $activity->causer?->name ?? '-' }} @if(! empty($activity->causer_role_label))<span class="{{ $activity->causer_role_class }} ms-2">{{ $activity->causer_role_label }}</span>@endif</p>
        <p><strong>Module:</strong> {{ $activity->properties['module'] ?? class_basename($activity->subject_type ?? $activity->log_name) }}</p>
        <p><strong>Activity:</strong> {{ $activity->display_title ?? $activity->description }}</p>
        <p><strong>Description:</strong> {{ $activity->display_description ?? '-' }}</p>
        <p><strong>Properties:</strong></p>
        <pre>{{ json_encode($activity->properties->toArray(), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
        <p><strong>IP Address:</strong> {{ $activity->properties['ip'] ?? '-' }}</p>
    </div>
</div>
@endsection
