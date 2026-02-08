# PusherSetting — LiteERP Extension

## Overview
- Manage Pusher credentials and broadcasting options.
- Provides a React page and dashboard menu hook for configuration.

## Features
- Read and save Pusher settings in `.env` (app_id, key, secret, cluster, host, port, scheme).
- Toggle broadcasting by setting `BROADCAST_CONNECTION=pusher`.
- Apply settings to Laravel broadcasting at runtime via service provider.

## Installation
1. Upload a ZIP whose root folder is `PusherSetting` and includes:
   - `extension.json`, `Install.php`, `ExtensionServiceProvider.php`
   - `Routes/web.php`, `Routes/api.php`, `Resources/js/autoload.js`
   - Optional views and models
2. After upload, the system runs the install plan and registers routes.

## Uninstallation
- Remove the extension folder from `extensions/` if necessary.

## Routes
- Web/API routes under `Routes/` are loaded by the service provider.
- React route is registered via `Resources/js/autoload.js`:
  - Path: `/pusher-setting`

## Configuration
- Settings are persisted directly to `.env` keys:
  - `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_APP_CLUSTER`, `PUSHER_HOST`, `PUSHER_PORT`, `PUSHER_SCHEME`, `BROADCAST_CONNECTION`.
- The service provider applies values to `broadcasting.connections.pusher.*` from `.env`.

## Packaging
- Keep ZIP size ≤ 10MB and MIME type `application/zip`.
- Root directory must match `extension.json.directory`.

## Compatibility
- Laravel 12, PHP 8.3, React + Vite.

