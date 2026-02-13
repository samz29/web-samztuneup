@extends('admin.layout')

@section('title', 'Settings')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">General Settings</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="app_name">Application Name</label>
                        <input type="text" class="form-control" id="app_name" value="SamzTune-Up">
                    </div>
                    <div class="form-group">
                        <label for="app_email">Contact Email</label>
                        <input type="email" class="form-control" id="app_email" value="admin@samztune-up.com">
                    </div>
                    <div class="form-group">
                        <label for="app_phone">Contact Phone</label>
                        <input type="text" class="form-control" id="app_phone" value="+62 123 456 7890">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment Settings</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="tripay_api_key">Tripay API Key</label>
                        <input type="password" class="form-control" id="tripay_api_key" placeholder="Enter API Key">
                    </div>
                    <div class="form-group">
                        <label for="tripay_private_key">Tripay Private Key</label>
                        <input type="password" class="form-control" id="tripay_private_key" placeholder="Enter Private Key">
                    </div>
                    <div class="form-group">
                        <label for="tripay_merchant_code">Merchant Code</label>
                        <input type="text" class="form-control" id="tripay_merchant_code" placeholder="Enter Merchant Code">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">System Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <span class="info-box-text">PHP Version</span>
                                <span class="info-box-number">{{ PHP_VERSION }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <span class="info-box-text">Laravel Version</span>
                                <span class="info-box-number">{{ app()->version() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <span class="info-box-text">Database</span>
                                <span class="info-box-number">MySQL</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <span class="info-box-text">Server OS</span>
                                <span class="info-box-number">{{ php_uname('s') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
