# Laravel `karl:make-multi-auth` Command


Este comando só pode ser usado no início do projeto, do contrário apresentará erro e também será necessário alterá-los manualmente. Futuramente pretendo fazer a busca pelos Models existentes e alterá-los ( veja mais em =>[Tasks](#tasks) ). Mas é o que tem pra hoje ;).

<!-- TOC -->

- [Laravel `karl:make-multi-auth` Command](#)
    - [Usage](#usage)
    - [Credits](#credits)
    - [Tasks](#tasks)

<!-- /TOC -->

## Usage

Se você armazenou em cache suas configurações, precisará executar

```bash
$ php php artisan config:cache
```
Veja uma lista completa dos arquivos criados ou afetados em [Multi Auth Files](multi-auth-files.md) 

Ao executar o comando artisan `karl:make-multi-auth {guard}`, você deve fornecer (opcionalmente) um nome para o `guard` como argumento. Ex;

```bash
$ php artisan karl::make-multi-auth {guard}
# O "guard" padrão é nomeado:'admin', não se esqueça de usar um nome de guard que atenda às suas necessidades.
#Este comando irá desenvolver configurações, controllers, middleware, migrations, models, factories, notifications, routes e visualizações; para você começar. 
```

**Check routes:** 

Para descobrir quais rotas foram criadas para seu `guard`

```bash
$ php artisan route:list
```

**Email verification:** 

You may require users to verify their email addresses before using the application. 
Read the [wiki](https://github.com/mtvbrianking/multi-auth/wiki/Email-Verification) on how to enable this.


## Credits

- [Amaral karl][link-author]
- [kallbuloso][link-kallbuloso]
- [Brian Matovu][link-brian-matovu]

## Tasks

-   Criar autenticação simples (sem confirmação de e-mail)

## License

The MIT License (MIT). Please see [License File](/license.md) for more information.

[link-author]: https://github.com/kallbuloso
[link-kallbuloso]: http://kallbuloso.com.br
[link-brian-matovu]: https://github.com/mtvbrianking/multi-auth
