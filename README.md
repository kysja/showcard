# ShowCard

An Android app that shows your text full screen, so you can hold up your phone and have it read from across the room. Use it for a name at the airport, an email address, a phone number or a short message.

## Features

- **Save text to reuse:** names, emails, phone numbers, links or anything else, up to 500 characters
- **Full-screen display:** the phone rotates to landscape and the text is sized to fill the screen, with shorter text shown bigger
- **Line breaks:** text appears on the lines you typed it
- **Fonts:** Standard or Mono
- **Colors:** White on Black or Black on White
- **Private and offline:** everything stays on your phone, with no account or internet connection needed

Double-tap the full-screen view to close it.

> Your items are stored only on the device. The app has no backup or export, and uninstalling it normally deletes them.

## Built with

- [NativePHP Mobile](https://nativephp.com/docs/mobile) v4, which renders real native screens from PHP with no web view
- [Laravel](https://laravel.com) 13
- SQLite for on-device storage
- A small local plugin, [`packages/aulog/screen-orientation`](packages/aulog/screen-orientation), for rotating the screen

## Running it yourself

You need PHP 8.3 or newer, Composer, the Android SDK and Java 17.

```sh
git clone https://github.com/kysja/showcard.git
cd showcard
composer setup
```

In `.env`, set the app name and ID:

```dotenv
APP_NAME=ShowCard
NATIVEPHP_APP_ID=com.example.showcard
```

Install the native project, then run the app on a connected phone or emulator:

```sh
php artisan native:install
php artisan native:run android
```

## Building an install file

This produces a signed APK you can share directly, without an app store.

1. Create a signing key. It's saved in `credentials/`, and its details are added to `.env`:

   ```sh
   php artisan native:credentials android
   ```

   Set `ANDROID_KEYSTORE_FILE` in `.env` to include the folder, for example `credentials/app-release-key.jks`.

2. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`.

3. Set the version:

   ```sh
   php artisan native:release major
   ```

4. Build the APK:

   ```sh
   php artisan native:package android
   ```

The file is created at `nativephp/android/app/build/outputs/apk/release/app-release.apk`.

For each update, run `php artisan native:release patch`, raise `NATIVEPHP_APP_VERSION_CODE` by 1 in `.env`, and build with the same key. Android rejects updates signed with a different key, so keep a backup of `credentials/` and its passwords.

## Tests

```sh
php artisan test
```

## How the code is organized

| Path | What's there |
| --- | --- |
| `routes/mobile.php` | The app's screens and their addresses |
| `app/NativeComponents/` | Screen logic: `ItemList`, `ItemForm` and `ItemShow` |
| `resources/views/native/` | Screen layouts |
| `app/Models/Item.php` | The saved item |
| `app/Enums/` | Fonts, color schemes and item types |

## License

[MIT](LICENSE.md)
