@extends('auth.layout')

@section('content')
    <p>Digite suas credenciais abaixo de clique em "Logar", ou clique em "Cadastrar" para criar uma nova conta.</p>
    <form role="form" method="post" action="{{ url('/login') }}">
        {!! csrf_field() !!}

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


        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember"> Lembrar-me
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-default">Login</button>
        <a href="{{ url('/register') }}" class="btn btn-primary">Cadastrar</a>
    </form>
@stop