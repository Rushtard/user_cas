
# user_cas

## Introduction

`user_cas` adds **CAS authentication support** to Nextcloud / ownCloud using the **phpCAS** library.

The application allows users authenticated by a CAS server to log into Nextcloud or ownCloud and optionally:

- Automatically create users on first login
- Synchronize user attributes
- Map CAS attributes to platform attributes
- Assign groups and quotas
- Restrict access based on CAS groups

Authentication typically uses the endpoint:

    /apps/user_cas/login

which is used as the CAS **service URL**.

---

# Compatibility

| Component | Supported |
|-----------|-----------|
| Nextcloud | ≥ 21 – 33 |
| ownCloud  | ≥ 10 |
| PHP       | ≥ 7.3 (tested up to PHP 8.x) |

---

# Installation

Two installation methods are supported.

---

# Method 1 — Standard Installation (Recommended)

Install the application from a **release archive** or the **Nextcloud App Store**.

### Steps

1. Download the latest release.
2. Extract the archive.
3. Ensure the directory name is `user_cas`.
4. Copy it into your apps directory:

    nextcloud/apps/user_cas

5. Fix permissions (example for Debian systems):

    chown -R www-data:www-data user_cas

6. Enable the application in the admin panel:

    Settings → Apps → Disabled Apps → CAS user and group backend

7. Configure the application:

    Settings → Administration → Security

---

# Method 2 — Installation via Git (Development)

Clone the repository:

    git clone https://github.com/Rushtard/user_cas.git

Copy the folder into your platform's apps directory.

Install dependencies:

    composer install --no-dev

Dependencies including phpCAS will be installed into the `vendor/` directory.

---

# Alternative: Using bundled dependencies

Some releases include the required dependencies in the `vendor/` directory.

If this is the case, **Composer is not required**.

---

# CAS Server Configuration

Important configuration values:

| Setting | Description |
|-------|-------------|
CAS Server Version | Usually `3.0`
CAS Server Hostname | Hostname of your CAS server
CAS Server Port | Usually `443`
CAS Server Path | Often `/cas`
Service URL | Must end with `/apps/user_cas/login`

Example:

    https://nextcloud.example.org/apps/user_cas/login

---

# Basic Settings

### Force CAS Login

If enabled, visiting Nextcloud will immediately redirect users to the CAS login page.

Useful when CAS should be the **only authentication mechanism**.

---

### Disable CAS Logout

If enabled, logging out of Nextcloud **does not log the user out of CAS**.

---

### Auto-create users

If enabled, users authenticated via CAS will automatically be created in Nextcloud.

If disabled:

- the user must already exist
- otherwise login will fail with **403 Forbidden**

---

### Update user data

When enabled, these attributes are updated at each login:

- Display name
- Email
- Groups
- Quota

---

# Attribute Mapping

CAS attributes can be mapped to platform attributes.

| Platform field | CAS attribute |
|----------------|---------------|
User ID | Custom user identifier |
Email | Email attribute |
Display Name | Name or combined attributes |
Group | Group attribute |
Quota | Storage quota |

If **User-ID mapping is empty**, the default CAS username is used.

---

# Groups

The application can synchronize groups from CAS.

Options include:

- Default group for new users
- Authorized CAS groups
- Group quotas
- Locked groups (never removed)

If **Authorized CAS Groups** is defined:

Users must belong to at least one of those groups to access the instance.

Otherwise login will fail with:

    403 Forbidden

---

# phpCAS Library

The application uses the **phpCAS** library.

Default behaviour:

- Uses bundled phpCAS via Composer
- Automatically loaded from `vendor/`

Optional configuration:

**Overwrite phpCAS path (CAS.php file)** allows specifying a custom path, for example:

    /usr/share/php/CAS.php

---

# Troubleshooting

## Login redirects but fails with 403

Possible causes:

- User does not exist and **autocreate is disabled**
- User not in **Authorized CAS Groups**
- Incorrect **User-ID mapping**

---

## phpCAS library could not be loaded

Ensure that the following directory exists:

    vendor/jasig/phpcas

If not, run:

    composer install

---

# OCC Commands

The application provides several OCC commands.

### Create user

    occ cas:create-user <uid>

### Update user

    occ cas:update-user <uid>

### Import users from Active Directory

    occ cas:import-users-ad

These commands support additional parameters such as:

- display name
- email
- groups
- quota
- enabled status

---

# License

AGPL v3 or later

---

# Authors

Current Version, since 1.11.0:
* Rushtard - [github.com/Rushtard](https://github.com/Rushtard)

Original project, 1.4.0-1.10.0:

* Felix Rupp - [github.com/felixrupp](https://github.com/felixrupp)

Older Versions:
* Sixto Martin Garcia - [github.com/pitbulk](https://github.com/pitbulk)
* David Willinger (Leonis Holding) - [github.com/leoniswebDAVe](https://github.com/leoniswebDAVe)
* Florian Hintermeier (Leonis Holding)  - [github.com/leonisCacheFlo](https://github.com/leonisCacheFlo)
* brenard - [github.com/brenard](https://github.com/brenard)

Additional contributors are listed in the Git history.

---

# Repository

https://github.com/Rushtard/user_cas
