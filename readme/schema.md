# Laravel `karl:make-schema` Command

Este comando só pode ser usado no início do projeto, do contrário apresentará erro na `Base de dados` e também será necessário alterá-los manualmente. Este comando é para ser usado com projeto em produção (como sempre neste package). Muito útil para corrigir o erro de MySQL nas 'migrations' durante o desenvolvimento. É o que tem pra hoje ;).

Código criado por [Amaral karl][link-author]

<!-- TOC -->

- [Laravel `karl:make-schema` Command](#laravel)
    - [Usage](#usage)
    - [Credits](#credits)
    - [Tasks](#tasks)

<!-- /TOC -->

## Usage

Ao executar o comando artisan `karl:make-schema` será executado 

```bash
$ php artisan karl:make-schema
```

## Credits

- [Amaral karl][link-author]
- [kallbuloso][link-kallbuloso]

## Tasks

-   Adicionar command revert-schema

## License

The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.

[link-author]: https://github.com/kallbuloso
[link-kallbuloso]: http://kallbuloso.com.br
