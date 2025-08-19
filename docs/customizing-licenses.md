# Customizing Licenses

The Republication Tracker Tool provides filters that allow developers to programmatically add, remove, or modify the available licenses.

## Adding Custom Licenses

You can add custom licenses using the `republication_tracker_tool_licenses` filter:

```php
function add_custom_licenses( $licenses ) {
    // Add a custom license
    $licenses['my-custom-license'] = [
        'label' => 'My Custom License',
        'description' => 'My Custom License Description',
        'url' => 'https://example.com/my-license',
        'badge' => 'https://example.com/my-license-badge.png'
    ];

    return $licenses;
}
add_filter( 'republication_tracker_tool_licenses', 'add_custom_licenses' );
```

## Modifying Existing Licenses

You can modify or remove existing licenses:

```php
function modify_licenses( $licenses ) {
    // Remove a license
    unset( $licenses['cc-zero-1.0'] );

    // Modify an existing license
    $licenses['cc-by-4.0']['description'] = 'Modified CC BY 4.0 description';

    return $licenses;
}
add_filter( 'republication_tracker_tool_licenses', 'modify_licenses' );
```

## Changing the Default License

You can change the default license using the `republication_tracker_tool_default_license` filter:

```php
function change_default_license( $default_license ) {
    return 'cc-by-4.0'; // Change default to CC BY 4.0
}
add_filter( 'republication_tracker_tool_default_license', 'change_default_license' );
```

## License Array Structure

Each license in the array should have the following structure:

- **label** (string, required): Display label for the license
- **description** (string, required): Full description of the license
- **url** (string, required): URL to the license text
- **badge** (string, optional): URL to the license badge image
