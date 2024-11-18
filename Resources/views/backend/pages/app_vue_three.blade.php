@extends(((version_compare(config('vaahcms.version'), '2.0.0', '<' )) ? 'vaahcms::backend.vaahone.layouts.backend' : 'vaahcms::backend.vaahtwo.layouts.backend' ))


@section('vaahcms_extend_backend_css')

    {{--        <link href="{{vh_module_assets_url("Appointment", "build/index.css")}}" rel="stylesheet" media="screen">--}}

@endsection


@section('vaahcms_extend_backend_js')

    @if(env('APP_MODULE_SAAS_ENV') == 'develop')
        <script type="module" src="http://localhost:8811/main.js"></script>
    @else
        <script type="module" src="{{vh_module_assets_url("Saas", "build/index.js")}}"></script>
    @endif

@endsection

@section('content')

    <div class="primevue">
        <div id="appSaas">


        </div>
    </div>

@endsection
