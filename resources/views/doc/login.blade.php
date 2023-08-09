@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('doc.processlogin') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('operatorid') ? ' has-error' : '' }}">
                            <label for="operatorid" class="col-md-4 control-label">Operator ID</label>

                            <div class="col-md-6">
                                <input id="operatorid" type="text" class="form-control" name="operatorid" value="{{ old('operatorid') }}" required autofocus style="text-transform: uppercase;">

                                @if ($errors->has('operatorid'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('operatorid') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('keyhash') ? ' has-error' : '' }}">
                            <label for="keyhash" class="col-md-4 control-label">KeyHash</label>

                            <div class="col-md-6">
                                <input id="keyhash" type="keyhash" class="form-control" name="keyhash" required>

                                @if ($errors->has('keyhash'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('keyhash') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
