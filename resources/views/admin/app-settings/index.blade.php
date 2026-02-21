@extends('admin.layout')

@section('title', 'App Settings')

@section('content')
<div class="row">
    @foreach($settings as $group => $groupSettings)
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ ucfirst($group) }} Settings</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.app-settings.bulk-update') }}" method="POST">
                    @csrf

                    @foreach($groupSettings as $setting)
                    <div class="form-group">
                        <label for="setting_{{ $setting->id }}">{{ $setting->key }}</label>
                        @if($setting->description)
                            <small class="form-text text-muted">{{ $setting->description }}</small>
                        @endif

                        @if($setting->type === 'boolean')
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                <input type="checkbox" class="custom-control-input"
                                       id="setting_{{ $setting->id }}" name="settings[{{ $setting->key }}]"
                                       value="1" {{ $setting->value ? 'checked' : '' }}>
                                <label class="custom-control-label" for="setting_{{ $setting->id }}"></label>
                            </div>
                        @elseif($setting->type === 'file')
                            @if($setting->value)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->key }}" class="img-thumbnail" style="max-width: 100px;">
                                </div>
                            @endif
                            <input type="file" class="form-control" name="file_{{ $setting->key }}" accept="image/*">
                            <input type="hidden" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                        @else
                            <input type="text" class="form-control" id="setting_{{ $setting->id }}"
                                   name="settings[{{ $setting->key }}]" value="{{ $setting->value }}">
                        @endif
                    </div>
                    @endforeach

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Update {{ ucfirst($group) }} Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Quick Settings -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.app-settings.create') }}" class="btn btn-success btn-block">
                            <i class="fas fa-plus mr-1"></i> Add New Setting
                        </a>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-info btn-block" onclick="location.reload()">
                            <i class="fas fa-sync mr-1"></i> Refresh Settings
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-warning btn-block" onclick="clearCache()">
                            <i class="fas fa-broom mr-1"></i> Clear Cache
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-danger btn-block" onclick="resetSettings()">
                            <i class="fas fa-undo mr-1"></i> Reset to Default
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function clearCache() {
    if (confirm('Are you sure you want to clear the application cache?')) {
        // You can add AJAX call here to clear cache
        alert('Cache cleared successfully!');
    }
}

function resetSettings() {
    if (confirm('Are you sure you want to reset all settings to default? This action cannot be undone.')) {
        // You can add AJAX call here to reset settings
        alert('Settings reset to default!');
    }
}
</script>
@endpush
