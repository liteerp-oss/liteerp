# SSL Support Extension

## Overview

The **SSL Support** extension enforces HTTPS usage across the system by automatically switching application URLs to use the secure `https://` scheme.

It ensures that generated links, redirects, and system URLs consistently use SSL when the application is deployed in an SSL-enabled environment.

This extension is designed to improve URL consistency and security at the application level.

---

## Important Notes

- This extension DOES NOT install, configure, or generate SSL certificates.
- A valid SSL certificate must already be installed and properly configured on your server.
- This extension only adjusts the application URL scheme to HTTPS.
- If the domain, subdomain, or application path changes, you must manually update the `APP_URL` value in your environment configuration.

---

## Features

- Force HTTPS scheme for system URLs
- Automatically generate secure links using `https://`
- Improve security consistency across the application
- Lightweight and easy to enable

---

## How It Works

1. Install and configure a valid SSL certificate on your server.
2. Enable the SSL Support extension.
3. The system will automatically generate URLs using `https://`.

---

## Configuration Requirement

If you change:

- Domain
- Subdomain
- Application path

You must update the `APP_URL` value in your `.env` file.

Example:

    APP_URL=https://yourdomain.com


Failure to update `APP_URL` may cause incorrect URL generation.

---

## Use Cases

- Enforce HTTPS across the entire system
- Standardize secure URL generation
- Deploy the application behind an SSL-enabled server
