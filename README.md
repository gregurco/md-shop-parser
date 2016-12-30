# MD-SHOP-PARSER
The main scope of this project is to parse product prices from famous shops in Moldova. Let's watch the level of discount.

### Install

```
$ composer install
$ php bin/console doctrine:migration:migrate
$ npm install
$ ./node_modules/.bin/bower install
$ ./node_modules/.bin/gulp
```

### Collect data

| Command | Description |
| ------- | ----------- |
| php bin/console app:parse:bomba | Parse data from bomba.md |
| php bin/console app:parse:maximum | Parse data from maximum.md |
| php bin/console app:parse:foxmart | Parse data from foxmart.md |

