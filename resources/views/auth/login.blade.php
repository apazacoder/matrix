@section('title')
  Entrar a SIAL - YLB
@endsection

@extends('layouts.main')

@section('content')
  <div class="main-wrapper login-wrapper display-table" style="display:none">
    <div class="vertical-center">
      <div class="login-form-wrapper horizontal-center z-depth-2">
        <div class="center-align">
          <img class="logo" src="{{ asset('/images/logo-color.png') }}">
        </div>
        <form method="POST" action="{{ route('login') }}">
          {{ csrf_field() }}
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix mdi mdi-email"></i>
              <input name="email" id="email" type="text"
                     value="{{ old('email') }}"
                     autocapitalize="off"
                     autocomplete="off"
                     spellcheck="false"
                     autocorrect="off"
                     required autofocus>
              <label for="email">Correo electrónico</label>
            </div>
            @if ($errors->has('email'))
              <div class="col s12 error-info red-text text-darken-3 center">
                {{ $errors->first('email') }}
              </div>
            @endif
          </div>
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix mdi mdi-key"></i>
              <input name="password" id="password" type="password" required>
              <label for="password">Contraseña</label>
            </div>
            @if ($errors->has('password'))
              <div class="col s12 error-info red-text text-darken-3">
                {{ $errors->first('password') }}
              </div>
            @endif
            <div class="col s12 rememberme-wrapper">
              <label class="checkbox">
                <input type="checkbox" value="remember" name="remember" id="remember">
                <span>Recuérdame</span>
              </label>
            </div>
            <div class="center-align col s12">
              <button type="submit" class="btn waves-effect waves-light">Entrar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
