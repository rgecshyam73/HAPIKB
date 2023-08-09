@extends('layouts.api')

@section('css')
    <style>
        html,
        body{
            /*height: 100%;*/
            background: #000000 url("{{ asset('images/bg.png') }}") no-repeat center center;
            background-size: cover;
        }

        body > .grid{
            height: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="ui middle aligned center aligned grid">
        <div class="three column row">
            <div class="column">
            </div>

            <div class="column">
                <h2 class="ui teal image header">
                    <div class="content">
                        <h2 class="header">API Login</h2>
                    </div>
                </h2>

                <form class="ui large form" id="loginForm" method="post">
                    <div class="ui stacked segment">
                        <div class="field">
                            <div class="ui left icon input">
                                <i class="user icon"></i>
                                <input type="text" name="operatorid" autocomplete="operatorid" placeholder="Partner Web ID">
                            </div>
                        </div>
                        <div class="field">
                            <div class="ui left icon input">
                                <i class="lock icon"></i>
                                <input type="password" autocomplete="new-password" name="keyhash" placeholder="Secret Key">
                            </div>
                        </div>
                        <button class="ui fluid large teal submit button" type="submit">Login</button>
                    </div>
                </form>

                <div class="ui inverted segment">
                    Copyright &copy; 2019 HKB Gaming <br> All Rights Reserved
                </div>
            </div>
            
            <div class="column">

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function(){
           $("#loginForm").form({
               on: 'blur',
               fields: {
                   operatorid: {
                       identifier: 'operatorid',
                       rules: [
                           {
                               type: 'empty',
                               prompt: 'Please enter a value'
                           }
                       ]
                   },
                   keyhash: {
                       identifier: 'keyhash',
                       rules: [
                           {
                               type: 'empty',
                               prompt: 'Please enter a value'
                           }
                       ]
                   },
               }
           });
        });
    </script>
@endsection