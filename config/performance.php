<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Strict Eloquent Models
    |--------------------------------------------------------------------------
    |
    | When enabled outside production, Eloquent throws on lazy-loaded
    | relationships and on attributes that were silently discarded by
    | $fillable. That turns N+1 queries into an immediate, locatable error
    | instead of a slow page nobody notices.
    |
    | Off by default: this codebase still has known lazy loads, so flipping
    | it on globally would break existing screens. Turn it on per machine
    | (APP_STRICT_MODELS=true) while fixing a feature.
    |
    */

    'strict_models' => (bool) env('APP_STRICT_MODELS', false),

];
