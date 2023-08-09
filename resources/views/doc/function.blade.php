@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">Testing Function API {{ $name }}</div>

                <div class="panel-body">
                    @if (!in_array($name,['register1','transfer1','updatePlayerSetting1']))
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label for="functionparam" class="col-md-4 control-label">Params</label>

                                <div class="col-md-6">
                                    <input id="functionparam" type="text" class="form-control" name="functionparam">
                                    <input id="functionname" type="hidden" class="form-control" name="functionname" value="{{ $name }}">
                                </div>
                            </div>

                            @if ($name=="register")
                                <div class="form-group">
                                    <label for="functionparam" class="col-md-4 control-label">Example Params</label>

                                    <div class="col-md-6" style="word-wrap: break-word;">
                                        <p>operatorid={{ $partner->web_id }}&username={{ $partner->prefix }}_test2&amp;currency={{ $partner->code }}&language=en&fullname=test2&referral={{ $partner->prefix }}_test&email={{ 'test2@'.$partner->domainname }}&hash={{ hash('sha256',$partner->web_id.$partner->prefix.'_test2'.$partner->code.'en'.'test2'.$partner->prefix.'_test'.'test2@'.$partner->domainname.$partner->keyhash) }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="button" id="functiongo" class="btn btn-primary">
                                        GO
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="response" class="col-md-4 control-label">URL</label>

                                <div class="col-md-6">
                                    <pre id="functionurl" class="response" style="height: 60px;"></pre>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="response" class="col-md-4 control-label">FULL PARAMS</label>

                                <div class="col-md-6">
                                    <pre id="functionfullparams" class="response" style="height: 60px;"></pre>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="response" class="col-md-4 control-label">RESPONSE</label>

                                <div class="col-md-6">
                                    <pre id="functionresponse" class="response" style="height: 60px;"></pre>
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="color: red;">
                            Function is not available for testing
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
