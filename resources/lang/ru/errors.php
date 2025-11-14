<?php

return [
    // Authentication & Authorization
    'unauthenticated' => 'Необходима авторизация',
    'unauthorized' => 'Несанкционированный доступ',
    'forbidden' => 'Доступ запрещен',
    'do_not_have_permission' => 'Нет разрешения',

    // JWT Token Errors
    'token_expired' => 'Срок действия токена истек',
    'token_invalid' => 'Недействительный токен',
    'token_blacklisted' => 'Токен заблокирован',

    // Validation & Input Errors
    'validation_failed' => 'Ошибка валидации данных',
    'bad_request' => 'Неверный запрос',
    'invariant_violation' => 'Нарушение бизнес-правила',
    'email_has_been_activated_already' => 'Email уже был активирован',
    'user_has_already_been_activated' => 'Пользователь уже был активирован',
    'invalid_verification_token' => 'Неверный или истекший токен подтверждения',

    // Field Validation Messages
    'validation' => [
        'required' => 'Поле :field обязательно для заполнения',
        'email' => 'Поле :field должно быть корректным email адресом',
        'unique' => 'Такое значение :field уже существует',
        'min' => 'Поле :field должно содержать минимум :min символов',
        'max' => 'Поле :field не может содержать более :max символов',
        'confirmed' => 'Поле :field не совпадает с подтверждением',
        'string' => 'Поле :field должно быть строкой',
    ],

    // Resource Errors
    'not_found' => ':entity с ID :id не найден',
    'conflict' => 'Конфликт ресурсов',

    // Rate Limiting
    'too_many_requests' => 'Слишком много запросов. Попробуйте позже',
    'service_unavailable' => 'Сервис временно недоступен',
];
