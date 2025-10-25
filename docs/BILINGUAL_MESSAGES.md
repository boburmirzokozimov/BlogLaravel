# Bilingual Messages Implementation

## Overview
The application now supports bilingual messages in both English and Russian for all API endpoints.

## Structure

### Translation Files
Translation files are located in:
- `/resources/lang/en/messages.php` - English translations
- `/resources/lang/ru/messages.php` - Russian translations

### MessageResource
The `MessageResource` class provides bilingual message support through the `trans()` method:

```php
MessageResource::trans('successfully_logged_out');
```

This returns a JSON response like:
```json
{
  "message": {
    "en": "Successfully logged out",
    "ru": "Успешный выход из системы"
  }
}
```

## Available Messages

### Authentication
- `successfully_logged_out` - "Successfully logged out" / "Успешный выход из системы"
- `unauthorized` - "Unauthorized" / "Неавторизован"
- `unauthenticated` - "Unauthenticated" / "Не аутентифицирован"

### User Management
- `user_created_successfully` - "User created successfully" / "Пользователь успешно создан"
- `user_retrieved_successfully` - "User retrieved successfully" / "Пользователь успешно получен"
- `user_updated_successfully` - "User updated successfully" / "Пользователь успешно обновлен"
- `user_deleted_successfully` - "User deleted successfully" / "Пользователь успешно удален"
- `user_not_found` - "User not found" / "Пользователь не найден"

### Validation
- `validation_failed` - "Validation failed" / "Ошибка валидации"
- `invalid_credentials` - "Invalid credentials" / "Неверные учетные данные"

### General
- `success` - "Success" / "Успешно"
- `error` - "Error" / "Ошибка"
- `bad_request` - "Bad request" / "Неверный запрос"

## Usage in Controllers

```php
// Return a bilingual message
return MessageResource::trans('user_created_successfully')
    ->response()
    ->setStatusCode(201);
```

## Usage in Error Handlers

```php
return response()->json([
    'error' => [
        'en' => __('messages.unauthorized', [], 'en'),
        'ru' => __('messages.unauthorized', [], 'ru'),
    ]
], 401);
```

## Adding New Messages

1. Add the message key and translations to both files:
   - `/resources/lang/en/messages.php`
   - `/resources/lang/ru/messages.php`

2. Use the message in your controller:
   ```php
   return MessageResource::trans('your_new_message_key');
   ```

## Implementation Details

The `MessageResource::trans()` method:
1. Loads both English and Russian translation files directly
2. Retrieves the message for the given key from both files
3. Supports parameter replacement using `:placeholder` syntax
4. Returns a resource with both translations

This approach ensures translations are always loaded, even without Laravel's translation cache.

