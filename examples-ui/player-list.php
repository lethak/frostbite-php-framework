<?php

require_once(dirname(__FILE__).'/library/Lethak/Frostbite/Server.php');
require_once(dirname(__FILE__).'/config.php');





$errors = array();

try
{
	$server = new Lethak_Frostbite_Server($serverIp, $serverRconPort, $serverRconPassword);
	$players = $server->players->get();
	$vars = array_merge(
		$server->getVar('maxPlayers'),
		$server->getVar('friendlyFire')
	);
}
catch(Exception $error)
{
	echo('<br>Exception: ['.get_class($error).'] <pre>'.print_r($error->getMessage(), true).'</pre>');
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>PLAYER LIST</title>
		<meta charset="utf-8">

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js?v=fpf-1"></script>
		<script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js?v=fpf-1"></script>
		<link rel="stylesheet" type="text/css" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css?v=fpf-1">
	</head>

	<body>

		<div class="container">
			<h1><?php echo($server->label); ?></h1>
			<h2>Player List <span class="badge"><?php echo($players['count'].'/'.$vars['vars.maxPlayers']); ?></span></h2>
			<?php if(count($players['list'])>0): ?>
			<table class="table table-striped">

				<thead>
				<tr>
					<th>#</th>
					<?php foreach($players['fields'] as $field): ?>
					<th><?php echo($field); ?></th>
					<?php endforeach; ?>
				</tr>
				</thead>

				<tbody>
				<?php foreach($players['list'] as $player): ?>
				<tr data-name="<?php echo($player->name); ?>">
					<td width="15%">
						<div class="btn-toolbar">
							<div class="btn-group">
								<a data-action="kill" 		class="ask-confirm btn btn-default btn-xs">Kill</a>
								<a data-action="kick" 		class="ask-confirm btn btn-default btn-xs">Kick</a>
								<a data-action="quickban" 	class="ask-confirm btn btn-default btn-xs">Ban</a>
							</div>
						</div>
					</td>
					<?php foreach($player->toArray() as $field => $fieldData): ?>
						<td><?php echo($fieldData); ?></td>
					<?php endforeach; ?>
				</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php endif; ?>
		</div>


		<script type="text/javascript">
		$('.ask-confirm').on('click', function(e){
			return confirm($(this).text()+': Are you sure ?');
		});
		</script>

	</body>
</html>