<?php

namespace Gillyware\Postal\Providers;

use Gillyware\Postal\Packet;
use Illuminate\Support\ServiceProvider;

class PostalServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->autoBindPackets();
    }

    private function autoBindPackets(): void
    {
        $this->app->beforeResolving(function ($abstract, $parameters, $container) {
            // We only care about subclasses of Packet
            if (! is_string($abstract) || ! is_subclass_of($abstract, Packet::class)) {
                return;
            }

            // If it's already bound/instantiated, nothing to do
            if ($container->bound($abstract) || $container->resolved($abstract)) {
                return;
            }

            // Create the packet from the current request and cache as a shared instance
            /** @var class-string<Packet> $abstract */
            $instance = $abstract::from($container->make('request'));

            // Register as a singleton so subsequent resolutions hit the cache
            $container->instance($abstract, $instance);
        });
    }
}
