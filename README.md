# Google Maps Static API Tester Plugin for WordPress

A WordPress plugin that provides a user interface for testing and demonstrating the Google Maps Static API integration. This plugin allows you to easily generate static map images with custom markers and styling through the WordPress admin interface.

## Features

- Visual interface for static map generation
- Multiple map types:
    - Roadmap
    - Satellite
    - Terrain
    - Hybrid
- Comprehensive customization options:
    - Custom map dimensions (up to 2048x2048 pixels)
    - Adjustable zoom levels (0-21)
    - Multiple marker support
    - Location specification via coordinates or addresses
    - Real-time map preview
- One-click URL copying for generated maps

## Requirements

- PHP 7.4 or later
- WordPress 6.7.1 or later
- Google Maps API key with Static Maps API enabled

## Installation

1. Download or clone this repository
2. Place in your WordPress plugins directory
3. Run `composer install` in the plugin directory
4. Activate the plugin in WordPress
5. Add your Google Maps API key in Google > Maps Static

## Usage

1. Navigate to Google > Maps Static in your WordPress admin panel
2. Enter your Google Maps API key in the settings section
3. Configure map settings:
    - Choose map type (roadmap, satellite, terrain, hybrid)
    - Set zoom level (0-21)
    - Specify dimensions (width and height)
    - Enter location (coordinates or address)
    - Add markers (optional)
4. Generate and preview the static map
5. Copy the generated map URL

## Features in Detail

### Map Types
- Roadmap: Default street map view
- Satellite: Aerial/satellite imagery
- Terrain: Physical relief map
- Hybrid: Satellite imagery with road overlay

### Location Specification
- Support for latitude/longitude coordinates
- Address-based location targeting
- Center point customization

### Marker Management
- Multiple marker support
- Dynamic marker addition/removal
- Flexible location specification for each marker

### Customization Options
- Zoom Levels:
    - World view (level 0)
    - Continental view (levels 1-5)
    - Regional view (levels 6-10)
    - City view (levels 11-15)
    - Street view (levels 16-21)
- Dimensions:
    - Width up to 2048 pixels
    - Height up to 2048 pixels
    - Custom aspect ratios

### Output Features
- High-resolution map images
- Direct URL generation
- Immediate preview functionality
- One-click URL copying

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the GPL v2 or later License.

## Support

## Support

- Documentation: https://github.com/arraypress/google-maps-embed-plugin
- Issue Tracker: https://github.com/arraypress/google-maps-embed-plugin/issues