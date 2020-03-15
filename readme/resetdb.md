# Laravel `tools:db` Command


Este comando é para ser usado com projeto em produção (como sempre neste package). Muito útil para dar um 'refresh' nas 'migrations' durante o desenvolvimento. É o que tem pra hoje ;).

Código copiado de [Echo Wang.](https://github.com/wgqi1126/laravel-tools)

<!-- TOC -->

- [Laravel `karl:make-reset-db` Command](#laravel)
    - [Usage](#usage)
    - [Credits](#credits)
    - [Tasks](#tasks)

<!-- /TOC -->

## Usage

Ao executar o comando artisan `karl:make-reset-db` serão executados os comandos `migrate:fresh` e `db:seed`

```bash
$ php artisan karl:make-reset-db
# Com este serão executados os commandos; 'migrate:fresh' e 'db:seed'
```

## Credits

- [Amaral karl][link-author]
- [kallbuloso][link-kallbuloso]
- [Echo Wang](https://github.com/wgqi1126/laravel-tools)

## Tasks

-   Adicionar command clear-cache

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-author]: https://github.com/kallbuloso
[link-kallbuloso]: http://kallbuloso.com.br
