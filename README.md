# Optivine

## Development

### Composer

Install packages
`composer install`

### Build Assets

By default, React UI is used. To use Vue UI, set the constant `ISTIAQHOSSAIN_OPTIVINE_UI` to `'vue'` in `optivine.php`.

Navigate to ui directory
`cd assets/src/admin/react` or `cd assets/src/admin/vue`

Build assets
`pnpm install && pnpm run build`

### PHP Unit testing

Run
`vendor/bin/phpunit`
