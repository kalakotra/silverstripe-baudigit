<?php

declare(strict_types=1);

namespace Kalakotra\Baudigit\Traits;

use SilverStripe\Control\HTTPResponse;

/**
 * Provides a feature-toggle mechanism controlled by YAML config.
 * Add `use FeatureToggleTrait;` to any controller and call
 * `$this->checkEnabled()` at the top of `index()` / `init()`.
 */
trait FeatureToggleTrait
{
    /**
     * Whether this controller is enabled.
     * Override per controller class in _config/config.yml:
     *   Kalakotra\Baudigit\Controllers\SomeController:
     *     enabled: false
     *
     * @config
     */
    private static bool $enabled = true;

    /**
     * Returns a 404 HTTPResponse when the feature is disabled.
     * Call this at the top of init() in every controller.
     */
    protected function checkEnabled(): ?HTTPResponse
    {
        if (!static::config()->get('enabled')) {
            return $this->httpError(404, _t(
                'Kalakotra\Baudigit\Traits\FeatureToggleTrait.DISABLED',
                'This feature is not available.'
            ));
        }
        return null;
    }
}
