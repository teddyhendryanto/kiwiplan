@extends('layouts.auth')

@section('content')
  <div class="vertical-align-wrap">
    <div class="vertical-align-middle">
      <div class="auth-box ">
        <div class="left">
          <div class="content">
            <div class="header">
              <p class="lead">Silahkan Login</p>
            </div>
            <form class="form-auth-small" method="POST" action="{{ route('login') }}">
              {{ csrf_field() }}
              <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                <label for="signin-email" class="control-label sr-only">Username</label>
                <input type="text" name="username" class="form-control" id="signin-text" value="" placeholder="Username">
                @if ($errors->has('username'))
                  <span class="help-block">
                    <strong>{{ $errors->first('username') }}</strong>
                  </span>
                @endif
              </div>
              <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="signin-password" class="control-label sr-only">Password</label>
                <input type="password" name="password" class="form-control" id="signin-password" value="" placeholder="Password">
                @if ($errors->has('password'))
                  <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                  </span>
                @endif
              </div>
              <div class="form-group clearfix">
                <label class="fancy-checkbox element-left">
                  <input type="checkbox">
                  <span>Ingat Saya</span>
                </label>
              </div>
              <button type="submit" class="btn btn-primary btn-lg btn-block">LOGIN</button>
              <div class="bottom">
                <span class="helper-text"><i class="fa fa-lock"></i> <a href="#">Lupa Password?</a></span>
              </div>
            </form>
          </div>
        </div>
        <div class="right">
          <div class="overlay"></div>
          <div class="content text">
            <h1 class="heading">Free Bootstrap dashboard template</h1>
            <p>by The Develovers</p>
          </div>
        </div>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>

@endsection
