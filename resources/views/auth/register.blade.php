@extends('auth.layout')

@section('content')
    <p>Preencha o formulário abaixo para completar seu cadastro.</p>
    <form role="form" method="post" action="{{ url('/register') }}">
        {!! csrf_field() !!}

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label class="control-label" for="input-name">Nome</label>
            <input type="text" name="name" id="input-name" class="form-control" placeholder="Digite seu nome completo" value="{{ old('name' )}}" required>

            @if ($errors->has('name'))
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label class="control-label" for="input-email">E-mail</label>
            <input type="email" name="email" id="input-email" class="form-control" placeholder="Digite seu e-mail" value="{{ old('email' )}}" required>

            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <label class="control-label" for="input-password">Senha</label>
            <input type="password" name="password" class="form-control" id="input-password" placeholder="Digite sua senha">

            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
            <label class="control-label" for="input-password_confirmation">Confirmar Senha</label>
            <input type="password" name="password_confirmation" class="form-control" id="input-password" placeholder="Confirme sua senha">

            @if ($errors->has('password_confirmation'))
                <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
            @endif
        </div>


        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember"> Lembrar-me
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-default">Cadastrar</button>
        <a href="{{ url('/login') }}" class="btn btn-primary">Retornar ao Login</a>
    </form>
@stop