<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Konekt\Menu\Facades\Menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->concord->registerModel(\Konekt\User\Contracts\User::class, \App\Models\User::class);

        $this->app->booted(function () {
            $this->reorderMenu();
        });
    }

    private function reorderMenu(): void
    {
        $menu = Menu::get('appshell');
        if (!$menu) {
            return;
        }

        $items = $menu->items;

        if (!$items->has('mercadolibre_group')) {
            $mlGroup = $menu->addItem('mercadolibre_group', __('Mercado Libre'));

            $mlGroup->addSubItem('ml_dashboard', __('Resumen ML'), [
                'url' => '/admin/mercadolibre',
            ])->data('icon', 'pie-chart')->activateOnUrls('/admin/mercadolibre');

            $mlGroup->addSubItem('ml_products', __('Publicaciones'), [
                'url' => '/admin/mercadolibre/products',
            ])->data('icon', 'box')->activateOnUrls('/admin/mercadolibre/products*');

            $mlGroup->addSubItem('ml_orders', __('Pedidos ML'), [
                'url' => '/admin/mercadolibre/orders',
            ])->data('icon', 'shopping-cart')->activateOnUrls('/admin/mercadolibre/orders*');

            $mlGroup->addSubItem('ml_config', __('Configuración ML'), [
                'url' => '/admin/mercadolibre/config',
            ])->data('icon', 'settings')->activateOnUrls('/admin/mercadolibre/config*');
        }

        $items = $menu->items;
        $desiredOrder = ['shop', 'mercadolibre_group', 'crm_group', 'settings_group'];
        $reordered = $items->only([]);

        foreach ($desiredOrder as $name) {
            if ($items->has($name)) {
                $reordered->put($name, $items->get($name));
            }
        }

        foreach ($items->keys() as $key) {
            if (!$reordered->has($key)) {
                $reordered->put($key, $items->get($key));
            }
        }

        $menu->items = $reordered;
    }
}
