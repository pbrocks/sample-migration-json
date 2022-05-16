# Sample Structured Export

This plugin is forked from Time Inc Legacy brands to export to UDF

## Changes to plugin

### Add UUID generator

	`Generate_UUID::create_uuid();`

### Remove Language check

```php
	$language = $this->get_language( $post );
	if ( $language ) {
		$udf['language'] = $language;
	}
```

```php
/**
 * The the language associated with the content. Only return language
 * if it is explicitly set.
 *
 * @param \WP_Post $post The Post.
 * @return string
 */
public function get_language( $post ) {
	if ( ! taxonomy_exists( 'pesp-language' ) ) {
		return false;
	}

	$languages = get_the_terms( $post, 'pesp-language' );

	if ( empty( $languages ) ) {
		return false;
	}

	$languages = wp_list_pluck( $languages, 'name' );

	if ( in_array( 'Spanish', $languages, true ) ) {
		return 'es';
	}

	if ( in_array( 'English', $languages, true ) ) {
		return 'en-us';
	}
}
```

## UPDATING `/lib` files

Lib files come from composer, but you need to ensure you run the command without the composer autoloader:
```
composer update --no-dev --no-autoloader
```

## FREQUENTLY ASKED QUESTIONS
