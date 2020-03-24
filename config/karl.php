<?php

return [

    /**
     * // TODO: Criar comando de dependências iniciais
     *
     */
    "dependencies" => [
        // Instalação de pacotes "requires"
        "require" => [

        ],
        // Instalação de pacotes "requires-dev"
        "require-dev" => [

        ],
    ],

    /**
     * Model Path
     */
    'model_path' =>  'Models',

    /**
     * Auth and MultiAuth COnfigurations
     */
    'auth' => [

        /**
         * Create layouts path and scaffold
         * Defautl => false
         */
        'layout_path' => false,

        /**
         * Default Layouts Extends : layouts.app
         */
        'layouts_extends' => 'template::app',

        /**
         * Default Confirm
         */
        'confirm' => [

            'redirect_after_confirm' => '/',
        ],

        'page_home' => [

            /**
             * make page home
             * Default => true
             */
            'make_page' => true,

            /**
             * name page home
             * Default => nome
             */
            'name' => 'home',
        ],

        /**
         * Redirect page after login
         */
        'redirect_page' => '/dashboard',
    ],
];
