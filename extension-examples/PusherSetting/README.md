# Pusher Setting Extension for LiteERP

Runtime configuration for Pusher (broadcasting) used by LiteERP.

## Features

- Read current Pusher configuration from environment
- Save new configuration to `.env` and rebuild frontend assets
- Admin-only REST API endpoints
- Navigation entry integrated via hook

## Installation

The extension lives under `app/extensions/PusherSetting` and follows LiteERP's extension architecture.

## Web & API

- Web: `/dashboard/pusher-setting` renders the settings page
- API (requires login and admin):
  - `GET /api/pusher-setting/config` — Read current config
  - `POST /api/pusher-setting/save` — Save and rebuild assets

## Localization

Translations are under `lang/en/messages.php`. ServiceProvider registers the namespace `PusherSetting`.

## Notes

- Values are stored in `.env`. Ensure the file is writable.
- Command `php artisan app:npmbuild` is invoked after saving to refresh the frontend.
