@extends("vaahcms::backend.vaahone.layouts.backend")

@section('vaahcms_extend_backend_css')

@endsection


@section('vaahcms_extend_backend_js')

    @if(env('APP_MODULE_SAAS_ENV') == 'develop')
        <script src="http://localhost:8080/saas/assets/build/app.js" defer></script>
    @else
        <script src="{{vh_module_assets_url("Saas", "build/app.js")}}"></script>
    @endif

@endsection

@section('content')

    <div id="appSaas">

        <router-view></router-view>

        <vue-progress-bar></vue-progress-bar>

    </div>

@endsection
