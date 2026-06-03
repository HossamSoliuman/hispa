
@if (session()->has('success'))


    <div class="alert alert-success success-alert">
        <strong> {{ session()->get('success') }}</strong>.
    </div>
@endif

@if (session()->has('info'))


    <div class="alert alert-primary primary-alert">
        <strong> {{ session()->get('info') }}</strong>.
    </div>
@endif
@if(auth()->user() && auth()->user()->status ==0)
<div class="alert alert-warning d-flex align-items-center" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
    <div>
        الحساب غير مفعل، يرجى استكمال البيانات الاساسية اللازمة أو مراجعة مسؤول النظام.
    </div>
</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>أوه المفاجئة!</strong> {{ session()->get('error') }}.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())

    <div class="alert alert-danger">
        <strong>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </strong>    </div>

@endif
@if (session()->has('error'))
    <script>
        window.onload = function() {
            notif({
                msg: "لايمكن الوصول الى هذه الصفحة",
                type: "error"
            })
        }
    </script>
@endif
@if (session()->has('success'))
    <script>
        $(function() {
            $.notify("{{ session('success') }}", "success");
        });
    </script>
@endif

