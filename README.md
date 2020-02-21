# Big Bang - Desafio Backend

Teste de entrevista de emprego para ocupação do cargo de programador PHP na Big Bing Shop que consiste em 
criar um micro-serviço que receba requisições HTTP no formato REST que receba como parâmetro o nome de uma cidade ou uma combinação de latitude e longitude e retorne uma sugestão de playlist (array com o título das músicas) de acordo com a temperatura atual da cidade.

## Instalação

Após clonar ou baixar este repositório e extraí-lo, certifique-se que tenha o Docker e Docker-Compose instalado na sua máquina e que a porta 80 esteja disponível. 

Com tudo isso correto, basta executar o seguinte comando para criar o container da aplicação.

```bash
docker-compose up -d
```
Posteriormente, você deverá instalar as dependências do sistema com o composer (atente-se de executar no diretório ***app***).

```bash
composer install
```
## Uso
Pra consumir a API, basta você realizar uma requisição HTTP para as seguintes rotas, lembrando que você poderá optar por quais critérios você deseja fazer a consulta, sendo eles:
- Busca pelo nome da cidade;
```bash
http://localhost/api/playlist/city/<NOME DA CIDADE>
```
- Busca por coordenadas;
```bash
http://localhost/api/playlist/coordinates/<LATITUDE>/<LONGITUDE>
```

## Observações
 - A base do API foi construída em Laravel 6.x, portanto veja a documentação do Laravel para maiores informações;
 - Você poderá alterar as variáveis de ambiente no arquivo ***.env***,
## License
[MIT](https://choosealicense.com/licenses/mit/)
