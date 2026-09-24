<?php

namespace Aeunius\NumeroALetras;

use Aeunius\NumeroALetras\Contracts\Moneda;
use Aeunius\NumeroALetras\Support\Conversor;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class NumeroALetrasServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/numero-a-letras.php', 'numero-a-letras');

        // El conversor es inmutable, así que una sola instancia sirve para toda la aplicación.
        $this->app->singleton(Conversor::class, function (Application $app): Conversor {
            /** @var Repository $config */
            $config = $app->make('config');

            $conversor = (new Conversor)
                ->moneda($this->moneda($app, $config->string('numero-a-letras.moneda')))
                ->formatoCentavos($config->string('numero-a-letras.formato_centavos'))
                ->conector($config->string('numero-a-letras.conector'));

            return $config->boolean('numero-a-letras.mayusculas') ? $conversor->mayusculas() : $conversor->minusculas();
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/numero-a-letras.php' => config_path('numero-a-letras.php'),
            ], 'numero-a-letras-config');
        }
    }

    /**
     * Un código ISO ('USD') o una clase propia que implemente Contracts\Moneda.
     */
    private function moneda(Application $app, string $moneda): string|Moneda
    {
        $instancia = class_exists($moneda) ? $app->make($moneda) : null;

        return $instancia instanceof Moneda ? $instancia : $moneda;
    }
}
