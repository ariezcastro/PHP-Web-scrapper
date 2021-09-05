<?php 

require_once('scrapper.php');

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Web Crawler</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<!-- UIkit CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.7.0/dist/css/uikit.min.css" />
	<link rel="stylesheet" type="text/css" href="css/
	style.css">

	<!-- UIkit JS -->
	<script src="https://cdn.jsdelivr.net/npm/uikit@3.7.0/dist/js/uikit.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/uikit@3.7.0/dist/js/uikit-icons.min.js"></script>
</head>
<body>
	<div class="uk-container uk-margin-medium-top">
		<h3 class="uk-text-primary"><strong><?= $crawlUrl; ?></strong> </h3>
		<p><strong>Max Crawled Page:</strong> <?= $pageCrawl; ?> </p>
		<div class="uk-card uk-card-default uk-card-body">
			<?php $images = $internal = $external = $word_count = $avg_load = $text_len = 0; ?>
	<table class="uk-table uk-table-divider uk-table-striped">
		<thead>
			<tr>
				<th>
					Number of pages crawled
				</th>
				<th>
					Number of Unique Images
				</th>
				<th>
					Number of unique internal links
				</th>
				<th>
					Number of unique external links
				</th>
				<th>
					Avg page load
				</th>
				<th>
					Avg word count
				</th>
				<th>
					Avg Title length
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($json as $jsons){ 
				$images += count($jsons->images);
				$internal += count($jsons->internal);
				$external += count($jsons->external);
				$word_count += intval($jsons->word_count);
				$avg_load += $jsons->load;
				$text_len += intval(strlen($jsons->title));
			}?>
			<tr>
				
				<td>
					<?= $pageCrawl; ?>
				</td>
				<td>
					<?= $images; ?>
				</td>
				<td>
					<?= $internal; ?>
				</td>
				<td>
					<?= $external; ?>
				</td>
				<td>
					<?= $avg_load; ?>
				</td>
				<td>
					<?= $word_count; ?>
				</td>

				<td>
					<?= $text_len; ?>
				</td>
				
			</tr>
			
		</tbody>
	</table>
</div>

<div class="uk-card uk-card-default uk-card-body uk-margin-medium-top">
	<table class="uk-table uk-table-divider uk-table-striped">
		<thead>
			<tr>
				<th>
					Crawled Url
				</th>
				<th>
					Images
				</th>
				<th>
					Internal Links
				</th>
				<th>
					External Links
				</th>
				<th>
					Status code
				</th>
		
			</tr>
		</thead>
		<tbody>
			<?php foreach($json as $json_data){ ?>
			<tr>
				<td>
					<?= urldecode($json_data->url); ?>
				</td>
				<td>
					<?= count($json_data->images); ?>
				</td>
				<td>
					<?= count($json_data->internal); ?>
				</td>
				<td>
					<?= count($json_data->external); ?>
				</td>
				<td>
					<?= $json_data->status_code[0]; ?>
				</td>
				
				<?php
				
				$internal = $images = $external = array();

				$internal[] = $json_data->internal;
				$images[] = $json_data->images;
				$external[] = $json_data->external;
				?>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>

<div class="uk-card uk-card-default uk-card-body uk-margin-medium-top">
	<table class="uk-table uk-table-divider uk-table-striped">
		<thead>
			<tr>
				<th>
					Crawled images
				</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($json as $json_datas){ ?>
			<?php foreach($json_datas->images as $image){ ?>
				
			<tr class="img">

				<td>
					<?= $crawlUrl.urldecode($image); ?>
				</td>
				
			</tr>
			<?php } } ?>
		</tbody>
	</table>
</div>


	</div>
</body>
</html>