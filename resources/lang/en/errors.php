<?php

return [
    // Authentication & Authorization
    'unauthenticated' => 'Unauthenticated',
    'unauthorized' => 'Unauthorized access',
    'forbidden' => 'Access forbidden',
    
    // JWT Token Errors
    'token_expired' => 'Token has expired',
    'token_invalid' => 'Token is invalid',
    'token_blacklisted' => 'Token has been blacklisted',
    
    // Validation & Input Errors
    'validation' => 'Validation error',
    'validation_failed' => 'Validation failed',
    'bad_request' => 'Bad request',
    'invariant_violation' => 'Business rule violation',
    
    // Resource Errors
    'not_found' => ':entity not found with ID: :id',
    'conflict' => 'Resource conflict',
    
    // Rate Limiting
    'too_many_requests' => 'Too many requests. Please try again later',
    'service_unavailable' => 'Service temporarily unavailable',
];
