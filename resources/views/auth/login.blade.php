@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="section">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="panel panel-default ragister-account account-login">
                        <div class="panel-body">
                            <h1 class="section-title title">@lang('user.title_login')</h1>
                            <div class="login-options text-center">
                                <a href="{{ url('login/facebook') }}" class="facebook-login"><i
                                            class="fa fa-facebook"></i> Login with Facebook</a>
                            </div>
                            <div class="devider text-center">Or</div>
                            {{ Form::open(['method'=>'post', 'url'=>url('/login'), 'class'=>'submit-ajax']) }}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                {{ Form::label(trans('user.email')) }}
                                <input type="text" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                  <strong>{{ $errors->first('email') }}</strong>
                              </span>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                {{ Form::label(trans('user.password')) }}
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                  <strong>{{ $errors->first('password') }}</strong>
                              </span>
                                @endif
                            </div>

                            <div class="checkbox">
                                <label class="pull-left"><input type="checkbox"
                                                                name="remember">@lang('user.remember_me')</label>
                                <a href="{{ url('/password/reset') }}"
                                   class="pull-right ">@lang('user.forgot_password')</a>
                            </div>

                            <div class="submit-button text-center">
                                {{ Form::button(trans('form.btn_login'), ['type'=>'submit', 'class'=>'btn btn-primary']) }}
                            </div>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
