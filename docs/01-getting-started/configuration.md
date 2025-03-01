You can configure the plugin in your site's `site/config/config.php` file:

```php
return [
    // ... other config options
    'moinframe.kirby-paradocs' => [
        // Set the slug of the index page
        // This is the page that will be used as the root for all plugin docs
        'index.slug' => 'docs',

        // Set the title of the index page
        'index.title' => 'Documentation',

        // Hide index page
        // With this option set to true, the index page will not be accessible but the docs for each plugin will still be accessible
        'index.hide' => false,

        // Allow all plugins to be included
        // If false, only plugins listed in the safelist will be included
        'includeAll' => false,

        // Safelist plugins to be included in the docs
        // If null, all plugins will be included
        // e.g. ['myname/my-plugin', 'myname/my-other-plugin']
        'safelist' => null,

        // Set if docs should be public
        // If false, only logged in users will be able to access the docs
        // Be aware, that this might leak information about plugins installed on your site to unauthorized users
        'public' => false,

        // Set the syntax highlighter to use
        // Available options: 'phiki' (requires phiki/phiki package), 'simple'
        // If you choose 'phiki' as highlighter but don't have the package installed, the plugin will automatically fall back to 'simple' highlighter.
        'highlighter' => 'phiki',
    ]
];
```
