<?php

add_action( 'admin_menu', 'mixpanel_create_meta_box' );
function mixpanel_create_meta_box() {
	if ( function_exists( 'add_meta_box' ) ) {
		add_meta_box( 'mixpanel-event-label', 'MixPanel Event Label', 'mixpanel_event_box', 'page' );
		add_meta_box( 'mixpanel-event-label', 'MixPanel Event Label', 'mixpanel_event_box', 'post' );
	}
}
function mixpanel_event_box() {
	global $post;
	$mixpanel_event_label = get_post_meta( $post->ID, 'mixpanel_event_label', TRUE );
	?>
	<table class="form_table">
		<tr>
			<th width="30%"><label for="mixpanel_event_label">MixPanel Event Name</label></th>
			<td width="70%"><input type="text" size="60" name="mixpanel_event_label"
								   value="<?php echo $mixpanel_event_label; ?>" /></td>
		</tr>
	</table>
	<?php
}

add_action( 'save_post', 'mixpanel_update_event_label' );
function mixpanel_update_event_label( $post_id ) {
	if ( isset( $_POST['mixpanel_event_label'] ) ) {
		update_post_meta( $post_id, 'mixpanel_event_label', $_POST['mixpanel_event_label'] );
	}
}
