<?php

namespace App\Providers;

use Collective\Html\FormBuilder;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
Use Schema;

class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        FormBuilder::component('materialText', 'components.material.form.text', ['label', 'name', 'value', 'attributes']);
        FormBuilder::component('materialSelect', 'components.material.form.select', ['label', 'name', 'options', 'value', 'attributes']);


        view()->composer('*', function ($view) {
            $view->with('_admin', auth('admin')->user());
        });

        \Form::macro('materialText', function ($label, $name, $value, $error, $options = []) {

            ob_start();

            ?>
            <div class="form-line<?php echo $error ? ' error' : '' ?>">
                <?php echo \Form::label($name, $label) ?>
                <?php echo \Form::text($name, $value, array_merge(['class' => 'form-control'], $options)) ?>
            </div>

            <?php
            echo !empty($error) ? '<label for="' . $name . '" class="error">' . $error . '</label>' : '';

            return ob_get_clean();
        });

        \Form::macro('materialPassword', function ($label, $name, $error, $options = []) {

            ob_start();

            ?>
            <div class="form-line<?php echo $error ? ' error' : '' ?>">
                <?php echo \Form::label($name, $label) ?>
                <?php echo \Form::password($name,array_merge(['class' => 'form-control'], $options)) ?>
            </div>

            <?php
            echo !empty($error) ? '<label for="' . $name . '" class="error">' . $error . '</label>' : '';

            return ob_get_clean();
        });

        \Form::macro('materialPriceText', function ($label, $name, $value, $error, $options = []) {

            ob_start();

            ?>
            <div class="form-line<?php echo $error ? ' error' : '' ?>">
                <?php echo \Form::label($name, $label) ?>
                <div class="input-group">
                    <span class="input-group-addon">$</span>
                    <?php echo \Form::text($name, $value, array_merge(['class' => 'form-control'], $options)) ?>
                </div>
            </div>

            <?php
            echo !empty($error) ? '<label for="' . $name . '" class="error">' . $error . '</label>' : '';

            return ob_get_clean();
        });

        \Form::macro('materialTextArea', function ($label, $name, $value, $error, $options = []) {

            ob_start();

            ?>
            <div class="form-line<?php echo $error ? ' error' : '' ?>">
                <?php echo \Form::label($name, $label) ?>
                <?php echo \Form::textarea($name, $value, array_merge(['class' => 'form-control'], $options)) ?>
            </div>

            <?php
            echo !empty($error) ? '<label for="' . $name . '" class="error">' . $error . '</label>' : '';

            return ob_get_clean();
        });

        \Form::macro('materialSelect', function ($label, $name, $selectOptions, $value, $error, $options = []) {

            ob_start();

            ?>
            <?php echo \Form::label($name, $label) ?>
            <?php echo \Form::select($name, $selectOptions, $value, array_merge(['class' => 'form-control show-tick'], $options)) ?>

            <?php
            echo !empty($error) ? '<label for="' . $name . '" class="error">' . $error . '</label>' : '';

            return ob_get_clean();
        });

        \Form::macro('materialFile', function ($label, $name, $error) {

            ob_start();

            ?>
            <?php echo \Form::label($name, $label) ?>
            <?php echo \Form::file($name) ?>

            <?php
            echo !empty($error) ? '<label for="' . $name . '" class="error">' . $error . '</label>' : '';

            return ob_get_clean();
        });


        Passport::routes();
    }
}
