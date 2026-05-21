# WPConstructor Plugin Version

WPConstructor Plugin Version is a lightweight PHP utility for WordPress plugins that allows you to **retrieve the version of a plugin’s main file safely**, without triggering translation errors or requiring manual inclusion of `plugin.php` on the frontend.

It also includes **built-in checks for PHP and WordPress version requirements**, and displays an admin notice if the current environment does not meet the requirements. You can optionally force the plugin to always run using a constant.

---

## Features

* Safely retrieves the version of a plugin’s main file.
* Avoids `get_plugin_data()` issues before the `init` hook.
* No need to include `plugin.php` manually on the frontend.
* Checks PHP and WordPress version requirements.
* Shows admin notice to users with `install_plugins` capability if requirements are not met.
* Optionally force plugin execution even if requirements are not met via a constant.

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
$main_file = __FILE__;

$plugin_version = require __DIR__ . '/vendor/wpconstructor/plugin-version/src/includes/plugin-version.php';

// If requirements are not met and the constant WPCN_PLUGIN_VERSION_ALWAYS_RUN is not set or false, stop execution.
if ( false === $plugin_version ) {
    return; // Stop execution if requirements are not met.
}

// Use the plugin version, e.g., define a constant.
define( 'MY_PLUGIN_VERSION', $plugin_version );
```

> Setting `WPCN_PLUGIN_VERSION_ALWAYS_RUN` to `true` will bypass PHP and WordPress version checks, ensuring the plugin always loads.

---

## Admin Notice

If the current PHP or WordPress version does not meet the requirements, an admin notice is displayed **only for users with the `install_plugins` capability**, unless `WPCN_PLUGIN_VERSION_ALWAYS_RUN` is set to `true`.

---

## License

GPL 3.0 or later. See [LICENSE](LICENSE.md) for details.

---

## Changelog

Version 1.0.3 (May 2026)

* Changed license from MIT to GPL 3.0 or later.
* Changed `composer.json` description.

Version 1.0.2 (March 2026)

* Removed index.php

Version 1.0.1 (March 2026)

* Corrected constant typo.
* Updated README.md

Version 1.0.0 (March 2026)

* Initial release.

---

## Author

WPConstructor – [WPConstructor.com](https://wpconstructor.com/)
