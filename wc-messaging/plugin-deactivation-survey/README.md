## Synopsis

This library will show a de-activation survey dialog when the user clicks "deactivate" on plugins.php on any plugins you've bound the library to. The form data will be posted to ccplugins.co

## Installation

- Copy the files into your project (I recommend using the path /lib/packages/plugin-deactivation-survey) excludes base general files(ie: .git)
- In deactivate-feedback-form.php:
	- Change plugin-text-domain.
- Include deactivate-feedback-form.php in your plugin base page (ie: index.php / plugin-name.php)
- Run the code below with your plugins slug

## Usage

	add_filter('sgits_deactivate_feedback_form_plugins', function($plugins) {
		$plugins[] = (object)array(
			'slug'		=> 'plugin-text-domain',
			'version'	=> 'plugin-version'
		);
		return $plugins;
	});

## Notes

You can include any other arbitrary data you might want to store in the object you pass to the $plugins array in the example above
