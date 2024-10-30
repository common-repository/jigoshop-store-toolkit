<p class="description"><?php _e( 'Use this form to enter All in One SEO Pack details for this Product.', 'jigo_st' ); ?></p>
<table>

	<tr>
		<td scope="row" style="padding:0 0 0.5em 0;">
			<label for="jigo_st_aioseop_title"><?php _e( 'Title', 'jigo_st' ); ?></label>
		</td>
		<td>
			<input type="text" id="jigo_st_aioseop_title" name="aioseop_title" value="<?php echo $title; ?>" size="62" />
			<p class="description"><?php _e( 'Most search engines use a maximum of 60 chars for the title.', 'jigo_st' ); ?></p>
		</td>
	</tr>

	<tr>
		<td scope="row" style="padding:0 0 0.25em 0;">
			<label for="jigo_st_aioseop_description"><?php _e( 'Description', 'jigo_st' ); ?></label>
		</td>
		<td>
			<textarea id="jigo_st_aioseop_description" name="aioseop_description" rows="3" cols="60"><?php echo $description; ?></textarea>
			<p class="description"><?php _e( 'Most search engines use a maximum of 160 chars for the description.', 'jigo_st' ); ?></p>
		</td>
	</tr>

	<tr>
		<td scope="row" style="padding:0 0 0.25em 0;">
			<label for="jigo_st_aioseop_keywords"><?php _e( 'Keywords (comma separated)', 'jigo_st' ); ?></label>
		</td>
		<td>
			<input type="text" id="jigo_st_aioseop_keywords" name="aioseop_keywords" value="<?php echo $keywords; ?>" size="62" />
			<p class="description"><?php _e( 'Keywords are comma separated. For instance: clothes, mens, shirts, t-shirt, etc.', 'jigo_st' ); ?></p>
		</td>
	</tr>

	<tr>
		<td scope="row" style="padding:0 0 0.25em 0;">
			<label for="jigo_st_aioseop_title_atr"><?php _e( 'Title atrributes', 'jigo_st' ); ?></label>
		</td>
		<td>
			<input type="text" id="jigo_st_aioseop_title_atr" name="aioseop_titleatr" value="<?php echo $title_atr; ?>" size="62" />
		</td>
	</tr>

	<tr>
		<td scope="row" style="padding:0 0 0.25em 0;">
			<label for="jigo_st_aioseop_menu_label"><?php _e( 'Menu label', 'jigo_st' ); ?></label>
		</td>
		<td>
			<input type="text" id="jigo_st_aioseop_menu_label" name="aioseop_menulabel" value="<?php echo $menu_label; ?>" size="62" />
		</td>
	</tr>

</table>