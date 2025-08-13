@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if (session('warning'))
  <div class="alert alert-warning">{{ session('warning') }}</div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <div class="fw-semibold mb-1">Vui lòng kiểm tra lại:</div>
    <ul class="mb-0">
      @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
  <div class="alert alert-danger">
    <div class="fw-semibold mb-1">Vui lòng kiểm tra lại:</div>
    <ul class="mb-0">
      @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif
