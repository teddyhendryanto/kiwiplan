@if(session('status'))
  <div class="alert-container">
    <div class="alert alert-success alert-dismissible" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="alert-message">{{ session('status') }}</span>
    </div>
  </div>
@endif

@if(session('status-success'))
  <div class="alert-container">
    <div class="alert alert-success alert-dismissible" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="alert-message">{{ session('status-success') }}</span>
    </div>
  </div>
@endif

@if(session('status-warning'))
  <div class="alert-container">
    <div class="alert alert-warning alert-dismissible" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="alert-message">{{ session('status-warning') }}</span>
    </div>
  </div>
@endif

@if(session('status-danger'))
  <div class="alert-container">
    <div class="alert alert-danger alert-dismissible" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="alert-message">{{ session('status-danger') }}</span>
    </div>
  </div>
@endif

@if (count($errors) > 0)
  <div class="alert-container">
    <div class="alert alert-danger alert-dismissible" role="alert">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <ul>
  			@foreach ($errors->all() as $error)
  				<li>
            <span class="alert-message">{{ $error }}</span>
          </li>
  			@endforeach
  		</ul>
    </div>
  </div>
@endif
