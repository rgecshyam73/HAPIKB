<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>HKBGAMING</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style type="text/css">
        body {
            background-color: #09314b;
        }

        .sidenav {
            height: 100%;
            width: 240px;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 60px;
            background-color: #07283d;
            overflow-x: hidden;
        }

        .sidenav a {
            padding: 6px 8px 6px 8px;
            text-decoration: none;
            font-size: 16px;
            color: #d9a873;
            display: block;
        }

        .sidenav a:hover {
            color: #f1f1f1;
        }

        .main {
            width: calc(100% - 240px);
            margin-left: 240px;
        }
    </style>

    <script type="text/javascript">
        function IsJson(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>
<body>
    <header>
        <div style="position: fixed;top: 0;width: 100%;z-index: 222;">
            <div style="position: absolute; width: 100%;height: 60px;">
                <nav class="navbar navbar-static-top">
                    <div>
                        <div style="text-align: left;background-color: #07283d;height: 60px;">
                            <img src="{{ asset('images/logo.png') }}">
                            @if (session('opid'))
                                <span style="float: right;font-size: 38px;margin: 0 15px;color: #d9a873;">{{ strtoupper($partner->name) }}</span>
                            @endif
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <div style="margin-top: 60px;">
        @if (! session('opid'))
            <div id="app">
                <div class="clearfix" style="height: 30px;"></div>
                @yield('content')
            </div>
        @else
            <div class="">
                <div class="sidenav">
                    <a href="{{ route('doc.functionname',['name'=>'register']) }}">Register</a>
                    <a href="{{ route('doc.functionname',['name'=>'lobby']) }}">Open Lobby</a>
                    <a href="{{ route('doc.functionname',['name'=>'balance']) }}">Check Balance</a>
                    <a href="{{ route('doc.functionname',['name'=>'transfer']) }}">Fund Transfer</a>
                    <a href="{{ route('doc.functionname',['name'=>'check_trans']) }}">Check Fund Transfer</a>
                    <a href="{{ route('doc.functionname',['name'=>'updatePlayerSetting']) }}">Update Player Data</a>
                    <a href="{{ route('doc.functionname',['name'=>'checkPlayerIsOnline']) }}">Check Player Online Status</a>
                    <a href="{{ route('doc.functionname',['name'=>'getAllPlayerBalance']) }}">Get Total Balance All Player</a>
                    <a href="{{ route('doc.functionname',['name'=>'getOnlinePlayerCount']) }}">Get Total Online Player</a>
                    <a href="{{ route('doc.functionname',['name'=>'getJackpot']) }}">Get Jackpot</a>
                    <a href="{{ route('doc.functionname',['name'=>'getTransResult']) }}">Get Trans Result</a>
                    <a href="{{ route('doc.functionname',['name'=>'getBonusReferral']) }}">Get Referral Bonus</a>
                    <a href="{{ route('doc.functionname',['name'=>'getReferralPerDay']) }}">Get Referral Bonus Per Day</a>
                    <a href="{{ route('doc.functionname',['name'=>'getDownline']) }}">Get Downline Referral</a>
                    <a href="{{ route('doc.functionname',['name'=>'getTurnover']) }}">Get Player Turnover</a>
                    <a href="{{ route('doc.functionname',['name'=>'getDailyWinLose']) }}">Get Daily Winlose</a>
                    <a href="{{ route('doc.functionname',['name'=>'getTableName']) }}">Get Table / Room Name</a>
                    <a href="{{ route('doc.functionname',['name'=>'getNumberResults']) }}">Get Number Results</a>
                    <a href="{{ route('doc.functionname',['name'=>'getNumberDetails']) }}">Get Number Result Detail</a>
                    <a href="{{ route('doc.functionname',['name'=>'getMarketTime']) }}">Get Open & Close Time Game Togel</a>
                    <a href="{{ route('doc.functionname',['name'=>'getCurrentOutstandingBet']) }}">Get Current Outstanding Bet</a>
                    <a href="{{ route('doc.functionname',['name'=>'getCurrentOutstandingBetDetail']) }}">Get Current Outstanding Bet Detail</a>
                    <a href="{{ route('doc.functionname',['name'=>'getInvoiceTogel']) }}">Invoice Togel</a>
                    <a href="{{ route('doc.logout') }}">Logout</a>
                </div>
                <div class="main">
                    <div class="clearfix" style="height: 30px;"></div>
                    @yield('content')
                </div>
            </div>
        @endif
    </div>

    <footer>
        
    </footer>

    <!-- Scripts -->
    <script type="text/javascript">
        $.ajaxPrefilter(function(options, originalOptions, xhr){
            if (options.type=="POST") {
                options.headers = {
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                }
            }
        });

        if ($('#functiongo').length>0) {
            $('#functiongo').on('click', function() {
                name = $('#functionname').val();
                params = $('#functionparam').val();
                $.ajax({
                    url  : "{{ route('doc.functiontest') }}",
                    type : "POST",
                    data : {
                        name : name,
                        params : params
                    },
                    beforeSend: function() {
                        //$("#iconLoading").show();
                    },
                    success: function (output) {
                        $('#functionurl').html(output.url);
                        $('#functionfullparams').html(output.params);
                        if (IsJson(output.reponse)) {
                            $('#functionresponse').html(output.reponse);
                        }
                    },
                    complete : function() {

                    },
                    error: function () {
                        // DO SOMETHING
                    }
                });
            });
        }
    </script>
</body>
</html>
