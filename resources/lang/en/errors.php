<?php

return [
    // Authentication & Authorization
    'unauthenticated' => 'Unauthenticated',
    'unauthorized' => 'Unauthorized access',
    'forbidden' => 'Access forbidden',
    'do_not_have_permission' => 'Do not have permission',

    // JWT Token Errors
    'token_expired' => 'Token has expired',
    'token_invalid' => 'Token is invalid',
    'token_blacklisted' => 'Token has been blacklisted',

    // Validation & Input Errors
    'validation_failed' => 'Validation failed',
    'bad_request' => 'Bad request',
    'invariant_violation' => 'Business rule violation',
    'email_has_been_activated_already' => 'Email has already been activated',
    'user_has_already_been_activated' => 'User has already been activated',

    // Field Validation Messages
    'validation' => [
        'required' => 'The :field field is required',
        'email' => 'The :field must be a valid email address',
        'unique' => 'The :field has already been taken',
        'min' => 'The :field must be at least :min characters',
        'max' => 'The :field may not be greater than :max characters',
        'confirmed' => 'The :field confirmation does not match',
        'string' => 'The :field must be a string',
    ],

    // Resource Errors
    'not_found' => ':entity not found with ID: :id',
    'conflict' => 'Resource conflict',

    // Rate Limiting
    'too_many_requests' => 'Too many requests. Please try again later',
    'service_unavailable' => 'Service temporarily unavailable',
];
