# Laravel `karl:make-comando` Command

Este comando só pode ser usado no início do projeto, do contrário apresentará erro e também será necessário alterá-los manualmente. Futuramente pretendo fazer a busca pelos Models existentes e alterá-los ( veja mais em =>[Tasks](#tasks) ). Mas é o que tem pra hoje ;).

<!-- TOC -->

- [Laravel `karl:make-comando` Command](#)
    - [Usage](#usage)
    - [Credits](#credits)
    - [Tasks](#tasks)

<!-- /TOC -->

## Usage

Ao executar o comando artisan `karl:make-model-replace`, você deve fornecer um nome para o diretório das 'Models' como argumento. Ex;

```bash
$ php artisan karl::make-model-replace <Model_Name>
# Com este comando alteramos o diretório padrão dos 'Models', Ex;
# karl:make-model-replace Entities => Colocará os models existentes e os novos  (daqui em diante) no diretório 'app/Entities'.
```

Você também pode trocar/substituir o nome do diretório dos 'Models'.

```bash
$ php artisan karl:make-model-rewrite <Model_Name> <New_Model_Name>
# Com este comando alteramos o nome diretório existente dos 'Models', Ex;
# karl:make-model-rewrite Models => Colocará os models existentes e os novos (daqui em diante) no diretório 'app/Models'.
```

Para retornar os 'Models ao diretório padrão (App).

```bash
$ php artisan karl:make-model-default <Model_Name>
# Com este comando alteramos o nome diretório existente dos 'Models', Ex;
# karl:make-model-default Models => Retornará os models existentes do diretório'app/Models' em 'app' (o padrão do Laravel).
```

## Credits

- [Amaral karl][link-author]
- [kallbuloso][link-kallbuloso]

## Tasks

-   Alterar os Models em projetos existentes
-   Ao trocar o nome do Diretório, verificar e alterar outros models existentes além do padrão (User.php).
-   Ler as outras Tasks.

## License

The MIT License (MIT). Please see [License File](/license.md) for more information.

[link-author]: https://github.com/kallbuloso
[link-kallbuloso]: http://kallbuloso.com.br
