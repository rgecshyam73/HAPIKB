<div class="row">
	<table class="table">
		<tr>
			<th>Operator ID</th>
			<th>Game ID</th>
			<th>Periode</th>
		</tr>
		<tr>
			<td><input type="text" id="operatorid" value="{{ request('operatorid') }}"></td>
			<td><input type="text" id="game_id" value="{{ request('game_id') }}"></td>
			<td><input type="text" id="period" value="{{ request('period') }}"></td>
		</tr>
	</table>
	<button id="search">Submit</button>

	<div>
		@if (count($dataTransResults)>0)
			<table class="">
				<tr>
					<th>Datetime</th>
					<th>Username</th>
					<th>Status</th>
					<th>Amount</th>
					<th>Coin</th>
					<th>Balance</th>
					<th>Period</th>
					<th>Trans ID</th>
				</tr>
				@foreach ($dataTransResults['data'] as $dataq1)
					<tr class="details">
						<td>{{ $dataq1['datetime'] }}</td>
						<td>{{ $dataq1['username'] }}</td>
						<td>{{ $dataq1['status'] }}</td>
						<td>{{ $dataq1['amount'] }}</td>
						<td>{{ $dataq1['coin'] }}</td>
						<td>{{ $dataq1['balance'] }}</td>
						<td>{{ $dataq1['period'] }}</td>
						<td><a>{{ $dataq1['trans_id'] }}</a></td>
					</tr>
				@endforeach
			</table>
		@endif
	</div>

	{{ print_r($dataTransDetails) }}
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
	$('#search').click(function(){
		window.location.href = window.location.pathname+'?search=yes&operatorid='+$('#operatorid').val()+'&game_id='+$('#game_id').val()+'&period='+$('#period').val();
	});

	$('.details a').click(function(){
		console.log($(this).text());
		window.location.href = window.location.pathname+'?search=yes&operatorid='+$('#operatorid').val()+'&game_id='+$('#game_id').val()+'&period='+$('#period').val()+'&trans_id='+$(this).text();
	});
</script>