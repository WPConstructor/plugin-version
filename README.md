# WPConstructor Plugin Version

WPConstructor Plugin Version is a lightweight PHP utility for WordPress plugins that allows you to **retrieve the version of a plugin’s main file safely**, without triggering translation errors or requiring manual inclusion of `plugin.php` on the frontend.  

It also includes **built-in checks for PHP and WordPress version requirements**, and displays an admin notice if the current environment does not meet the requirements.

---

## Features

- Safely retrieves the version of a plugin’s main file.
- Avoids `get_plugin_data()` issues before the `init` hook.
- No need to include `plugin.php` manually on the frontend.
- Checks PHP and WordPress version requirements.
- Shows admin notice to users with `install_plugins` capability if requirements are not met.

---

## Installation

Install via Composer:

```bash
composer require wpconstructor/plugin-version
```

Or download from GitHub as a ZIP and include it in your plugin manually.

---

## Usage

Add the following code to your main plugin file:

```php
$main_file      = __FILE__;
$plugin_version = require __DIR__ . '/vendor/wpconstructor/plugin-version/src/includes/plugin-version.php';

// False when PHP or WordPress requirements are not satisfied.
if ( false === $plugin_version ) {
	return; // Stop execution if requirements are not met.
}

// Use the plugin version, e.g., define a constant.
define( 'MY_PLUGIN_VERSION', $plugin_version );
```

---

## Admin Notice

If the current PHP or WordPress version does not meet the requirements, an admin notice is displayed **only for users with the `install_plugins` capability**:

---

## License

MIT License. See [LICENSE](https://opensource.org/licenses/MIT) for details.

---

## Author

WPConstructor – [https://wpconstructor.com/contact](https://wpconstructor.com/contact)
