<?php

return [
    // Authentication & Authorization
    'unauthenticated' => 'Необходима авторизация',
    'unauthorized' => 'Несанкционированный доступ',
    'forbidden' => 'Доступ запрещен',
    
    // JWT Token Errors
    'token_expired' => 'Срок действия токена истек',
    'token_invalid' => 'Недействительный токен',
    'token_blacklisted' => 'Токен заблокирован',
    
    // Validation & Input Errors
    'validation' => 'Ошибка валидации',
    'validation_failed' => 'Ошибка валидации данных',
    'bad_request' => 'Неверный запрос',
    'invariant_violation' => 'Нарушение бизнес-правила',
    
    // Resource Errors
    'not_found' => ':entity с ID :id не найден',
    'conflict' => 'Конфликт ресурсов',
    
    // Rate Limiting
    'too_many_requests' => 'Слишком много запросов. Попробуйте позже',
    'service_unavailable' => 'Сервис временно недоступен',
];
