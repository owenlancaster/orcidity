<div class="container">
	<div class="row">
		<div class="span12 pagination-centered">
			<h2>ORCIDity - timeline mashup for:</h2>
			<h4><?php print $orcid_profile['givenname'] . " " . $orcid_profile['familyname'] . "<br />"; ?></h4>
			<br /><br /><hr>
			<div id="timeline-embed"></div>
			<script type="text/javascript">
				var timeline_config = {
					width:              '100%',
					height:             '600',
					source:             '<?php echo base_url("temp/json_temp.json"); ?>',
					embed_id:           'timeline-embed',               //OPTIONAL USE A DIFFERENT DIV ID FOR EMBED
					start_at_end:       false,                          //OPTIONAL START AT LATEST DATE
//					start_at_slide:     '4',                            //OPTIONAL START AT SPECIFIC SLIDE
					start_zoom_adjust:  '3',                            //OPTIONAL TWEAK THE DEFAULT ZOOM LEVEL
					hash_bookmark:      true,                           //OPTIONAL LOCATION BAR HASHES
					font:               'Bevan-PotanoSans',             //OPTIONAL FONT
					debug:              true,                           //OPTIONAL DEBUG TO CONSOLE
					lang:               'fr',                           //OPTIONAL LANGUAGE
					maptype:            'watercolor',                   //OPTIONAL MAP STYLE
					css:                '<?php echo base_url(CSS."timeline.css");?>',     //OPTIONAL PATH TO CSS
					js:                 '<?php echo base_url(JS."timeline.js");?>'    //OPTIONAL PATH TO JS
				}
			</script>
			<script type="text/javascript" src="<?php echo base_url(JS."storyjs-embed.js");?>"></script>
			<br /><br /><hr>
			<h4>Publication and citation summary table (citation summary uses ImpactStory API widget)</h4>
			<br />
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th>DOI</th>
						<th>Title</th>
						<th>Date</th>
						<th>Link</th>
						<th>Citations</th>
					</tr>
				</thead>
				<tbody>				
			<?php
			foreach ( $doi_dates as $date ) {
				if ( $date['title'] != "" ) {
					print "<tr>";
					print "<td>" . $date['doi'] . "</td>";
					print "<td>" . $date['title'] . "</td>";
					print "<td>" . $date['year'] . "</td>";
					print "<td>" . $date['link'] . "</td>";
					print '<td><div class="impactstory-embed" data-id="' . $date['doi'] . '" data-show-logo="false" data-verbose-badges="true" data-id-type="doi" data-api-key="' . $this->config->item('impactstory-api-key') . '"></div></td>';
					print "</tr>";
				}
//				print_r($date);
			}
			?>
				</tbody>
			</table>

		</div>
	</div>
</div>
	
	