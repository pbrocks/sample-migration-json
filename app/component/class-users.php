<?php
/**
 * Users Class
 *
 * @package    WordPress
 * @subpackage sample_structured_export
 */

namespace Phoenix\Sample_Structured_Export\Component;

use Phoenix\Sample_Structured_Export\Component as Component;

/**
 * Class Users
 */
class Users extends Component {

	/**
	 * Export Users information.
	 *
	 * @param \WP_User_Query $user The User.
	 * @param array          $udf UDF.
	 *
	 * @return \stdClass
	 */
	public static function export( $user, $udf = array() ) {
		$udf['firstName'] = $user->first_name;
		if ( '' != $user->last_name ) {
			$udf['lastName'] = $user->last_name;
		}
		if ( '' != $user->user_city ) {
			$udf['city'] = $user->user_city;
		}
		if ( '' != $user->user_state ) {
			$udf['state'] = $user->user_state;
		}
		$udf['displayName'] = $user->display_name;

		$avatar_id = get_user_meta( $user->ID, 'user_avatar', true );
		$udf['profileImage'] = $avatar_id ? wp_get_attachment_url( $avatar_id ) : get_avatar_url( $user->ID, array( 'size' => '240' ) );

		if ( '' != $user->user_url ) {
			$udf['websiteUrl'] = $user->user_url;
		}

		// map social links
		for ( $i = 1; $i <= 5; $i++ ) {
			$link = 'social_link_' . $i;
			$parse_url = parse_url( $user->$link ) ?? '';
			if ( isset( $parse_url['host'] ) ) {
				$parse_urls[] = $parse_url;
			}
		}
		// $insta_match = preg_grep('/instagram/i', array_column($parse_urls, 'host'));
		// if(!empty($insta_match) && is_array($insta_match)) {
		// $social['instagram'] = str_replace('/','',$parse_urls[key($insta_match)]['path']) ?? '';
		// }
		$fb_match = preg_grep( '/facebook/i', array_column( $parse_urls, 'host' ) );
		if ( ! empty( $fb_match ) && is_array( $fb_match ) ) {
			$social['facebookUsername'] = str_replace( '/', '', $parse_urls[ key( $fb_match ) ]['path'] ) ?? '';
		}
		$tw_match = preg_grep( '/twitter/i', array_column( $parse_urls, 'host' ) );
		if ( ! empty( $tw_match ) && is_array( $tw_match ) ) {
			$social['twitterHandle'] = str_replace( '/', '', $parse_urls[ key( $tw_match ) ]['path'] ) ?? '';
		}
		$pin_match = preg_grep( '/pinterest/i', array_column( $parse_urls, 'host' ) );
		if ( ! empty( $pin_match ) && is_array( $pin_match ) ) {
			$social['pinterestUsername'] = str_replace( '/', '', $parse_urls[ key( $pin_match ) ]['path'] ) ?? '';
		}
		// $tum_match = preg_grep('/tumblr/i', array_column($parse_urls, 'host'));
		// if(!empty($tum_match) && is_array($tum_match)) {
		// $parse_tumblr = explode('.',$parse_urls[key($tum_match)]['host']);
		// $social['tumblr'] = $parse_tumblr[0] ?? '';
		// }
		if ( is_array( $social ) && ! empty( $social ) ) {
			$udf['social'] = $social;
		}

		// $udf['newsletterId'] = ($user->newsletter_subscription == 1 || $user->newsletter_subscription == 'true') ? 1 : 0;
		if ( '' != $user->description ) {
			$udf['tagLine'] = $user->description;
		}
		// $udf['roles'] = $user->roles;

		// brandData field
		// $user_category_name = [];
		// foreach($user->user_categories as $cat_id) {
		// $user_category_name[] = get_the_category_by_ID( $cat_id );
		// }

		// $user_badge_name = [];
		// foreach($user->users_total_badges as $badge_id) {
		// $user_badge_name[] = get_the_category_by_ID( $badge_id );
		// }

		// $brand_data = array(
		// 'categories'    => $user_category_name,
		// 'invitesSent'   => $user->invites_sent,
		// 'invitedBy'     => $user->invited_by,
		// 'totalBadges'   => $user_badge_name
		// );
		// $udf['brandData'] = $brand_data;

		unset( $avatar_id, $user_category_name, $user_badge_name, $social, $parse_url, $parse_urls, $insta_match, $fb_match, $tw_match, $pin_match, $tum_match );

		return $udf;
	}
}
