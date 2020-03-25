# Laravel `karl:make-auth` Command

Este comando só pode ser usado no início do projeto, do contrário apresentará erro e também será necessário alterá-los manualmente. Futuramente pretendo fazer a busca pelos Models existentes e alterá-los ( veja mais em =>[Tasks](#tasks) ). Mas é o que tem pra hoje ;).

<!-- TOC -->

- [Laravel `karl:make-comando` Command](#)
    - [Usage](#usage)
    - [Credits](#credits)
    - [Tasks](#tasks)

<!-- /TOC -->

## Usage

```bash
$ php artisan karl::make-auth
# Com este comando criamos todos os arquivos necessários para autenicação
```

```bash
$ php artisan karl::make-auth --force
# Com este comando força a criar todos os arquivos necessários para autenicação mesmo que já existam.
```

```bash
$ php artisan karl::make-auth-confirm
# Com este comando criamos todos os arquivos necessários para confirmação da criação da conta por email
```

```bash
$ php artisan karl::make-auth-confirm --force
# Com este comando força a criar todos os arquivos necessários para confirmação da criação da conta por email mesmo que já existam.
```
## Credits

- [Amaral karl][link-author]
- [kallbuloso][link-kallbuloso]
- [Laravel website][link-laravel-website]

## Tasks

-   Criar autenticação full (com confirmação de e-mail)

## License

The MIT License (MIT). Please see [License File](/license.md) for more information.

[link-author]: https://github.com/kallbuloso
[link-kallbuloso]: http://kallbuloso.com.br
[link-laravel-website]: https://laravel.com/docs/frontend

