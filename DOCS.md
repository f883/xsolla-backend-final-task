# Документация API
1. Установка
2. Уровни доступа
3. Авторизация
4. API
  1. Авторизация
  2. Биржа
  3. Предметы
  4. Предложения на продажу и покупку
  5. Топ популярности
  6. Изменение параметров пользователей
5. Полезные команды

## Установка
1. Клонировать репозиторий
2. Установить зависимости `composer install`
3. Установить драйвер postgresql
4. Установить базу данных postgres. Для разработки использовался официальный docker контейнер с базой.
5. В файле Dependencies.php указать параметры подключения к базе
6. Если база данных пустая, то нужно сделать миграцию `vendor/bin/doctrine orm:schema-tool:update --force`
7. В директории проекта запустить сервер `php -S localhost:8000 -t public/`


## Уровни доступа
### Гость
Без авторизации имеет доступ к следающим методам:
* `/api/exchange`,
* `/api/top/users`,

### Пользователь
Требуется авторизация.
Имеет доступ ко всем методам, кроме тех, которые доступны только администратору.

### Администратор
Требуется авторизация.
Помимо доступа к методам пользователя может использовать следующие методы:
1. PUT `/api/exchange/deposit/user/{id}`
2. PUT `/api/exchange/withdraw/user/{id}`
3. POST `/api/items/types` 
4. POST `/api/users/{id}/items`
5. POST `/api/exchange/fee`
6. GET `/api/exchange/balance`
7. GET `/api/exchange/earn`


## Авторизация
Вход в приложение осуществляется по access_token. Он должен быть указан в теле запроса.
Для получения нового access_token используется refresh_token. 
Получение токенов происходит после авторизации через пару логин-пароль.
Пример запроса с испольхованием 

Access_token не требуется для запросов по следующим путям:
1. `/api/auth/register`,
2. `/api/auth/login`,
3. `/api/exchange`,
4. `/api/top/users`,
5. `/api/auth/updatetoken` (Требуется refresh_token)

## API
### Авторизация
#### Регистрация
POST `/api/auth/register`
Тело запроса должно содержать name и password.
Пример запроса:

`http://192.168.1.111:8000/api/auth/register`

```
{    
	"name": "Vasya",
	"password": "qwerqwer"
}
```
Ответ:
```
{
  "ok": "true",
  "user_id": 45
}
```

#### Вход в систему (получение токенов)
POST `/api/auth/login`

Пример запроса:

`http://192.168.1.111:8000/api/auth/login`

```
{    
	"name": "Vasya",
	"password": "qwerqwer"
}
```
Ответ:
```
{
  "ok": "true",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDIiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.zUdqW2b7KCU40Y_ldudumLUOrTjqQ0Ux8HDTWULSCSE",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoicmVmcmVzaF90b2tlbiIsInVzZXJJZCI6IjQyIiwiaXNzIjoiaHR0cDpcL1wvZXhhbXBsZS5vcmciLCJhdWQiOiJodHRwOlwvXC9leGFtcGxlLmNvbSIsImlhdCI6MTU2NzkwMDgwMCwibmJmIjoxNTY3OTAwODAwLCJleHAiOjE1NzA0OTI4MDB9.CQoLUUfCCy2Juir5vxHGdPSeG7CGpjcIqMuxeYWo-2s"
  }
}
```

#### Выход из системы (инвалидация всех токенов)
POST `/api/auth/logout`

Пример запроса:

`http://192.168.1.111:8000/api/auth/logout`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDIiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.zUdqW2b7KCU40Y_ldudumLUOrTjqQ0Ux8HDTWULSCSE"
}
```
Ответ:
```
{
  "ok": "true"
}
```

#### Обновление access_token
POST `/api/auth/updatetoken`

Пример запроса:

`http://192.168.1.111:8000/api/auth/updatetoken`

```
{
	"refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoicmVmcmVzaF90b2tlbiIsInVzZXJJZCI6IjQyIiwiaXNzIjoiaHR0cDpcL1wvZXhhbXBsZS5vcmciLCJhdWQiOiJodHRwOlwvXC9leGFtcGxlLmNvbSIsImlhdCI6MTU2NzkwMDgwMCwibmJmIjoxNTY3OTAwODAwLCJleHAiOjE1NzA0OTI4MDB9.8jxm1KzX6AikMXh7XNHY2ipuHHQQyt50kLms4MIlJiY"
}
```
Ответ:
```
{
  "ok": "true",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDIiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.ce_CPm80jD1n13mh3-3puKAl2Ra34aO6sXyJagEqz4c",
    "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoicmVmcmVzaF90b2tlbiIsInVzZXJJZCI6IjQyIiwiaXNzIjoiaHR0cDpcL1wvZXhhbXBsZS5vcmciLCJhdWQiOiJodHRwOlwvXC9leGFtcGxlLmNvbSIsImlhdCI6MTU2NzkwMDgwMCwibmJmIjoxNTY3OTAwODAwLCJleHAiOjE1NzA0OTI4MDB9.SP9VTYC4jW_uFmPTMlnOD5R3BH7SEP-JyJWFt5e1aSs"
  }
}
```


### Биржа
#### Получить статус биржи
GET `/api/exchange`

Пример запроса:

`http://192.168.1.111:8000/api/exchange`

Ответ:
```
{
  "ok": "true",
  "data": {
    "fee": 0.05,
    "users_count": 1,
    "orders_count": 0
  }
}
```

#### Установить налог на покупки
POST `/api/exchange/fee`

Пример запроса:

`http://192.168.1.111:8000/api/exchange/fee`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDIiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.fIrt4VKRCBVQBdanIw_Y79vmf6KG7cg41wtERk_PWJA",
	"data": {
		"fee": "0.1"
	}
}
```
Ответ:
```
{
  "ok": "true"
}
```

#### Получить баланс биржи
GET `/api/exchange/balance`

Пример запроса:

`http://192.168.1.111:8000/api/exchange/balance`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDIiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.fIrt4VKRCBVQBdanIw_Y79vmf6KG7cg41wtERk_PWJA"
}
```
Ответ:
```
{
  "ok": "true",
  "balance": 0
}
```

#### Получить прибыль биржи за заданный период времени
GET `/api/exchange/earn`

Пример запроса:

`http://192.168.1.111:8000/api/exchange/earn`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDIiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.fIrt4VKRCBVQBdanIw_Y79vmf6KG7cg41wtERk_PWJA",
	"data":{
		"from_date" : "20-01-2018",		
		"to_date" : "21-02-2019"
	}
}
```
Ответ:
```
{
  "earn": 0,
  "from_date": "20-01-2018",
  "to_date": "21-02-2019"
}
```

#### Добавить деньги на баланс пользователя
PUT `/api/exchange/deposit/user/{id}`

Пример запроса:

`http://192.168.1.111:8000/api/exchange/deposit/user/1`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDMiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.h03DSt2wnfm0vjldU-ravc2fd8X6y8tQAbj3N0YixbM",
	"data":{
		"value" : "20"
	}
}
```
Ответ:
```
{
  "ok": "true",
  "balance": 40
}
```

#### Снять деньги с баланса пользователя
PUT `/api/exchange/withdraw/user/{id}`

Пример запроса:

`http://192.168.1.111:8000/api/exchange/withdraw/user/1`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDMiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.h03DSt2wnfm0vjldU-ravc2fd8X6y8tQAbj3N0YixbM",
	"data":{
		"value" : "20"
	}
}
```
Ответ:
```
{
  "ok": "true",
  "balance": 0
}
```

### Предметы
#### Получить список существующих типов предметов
GET `/api/items/types`

Пример запроса:

`http://192.168.1.111:8000/api/items/types/1`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDMiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.h03DSt2wnfm0vjldU-ravc2fd8X6y8tQAbj3N0YixbM"
}
```
Ответ:
```
{
  "types": [
    {
      "id": 23,
      "name": "testss_312312sad31232s23"
    },
    {
      "id": 24,
      "name": "testss_312312sad3s123s2s23"
    },
    {
      "id": 25,
      "name": "testss_3123s12sad3s123s2s23"
    },
    {
      "id": 26,
      "name": "testss_3123s12sasadasdd3s123s2s23"
    }
  ]
}
```

#### Получить описание типа предмета по идентификатору
GET `/api/items/types/{id}`

Пример запроса:

`http://192.168.1.111:8000/api/items/types/23`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDMiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.h03DSt2wnfm0vjldU-ravc2fd8X6y8tQAbj3N0YixbM"
}
```
Ответ:
```
{
  "item_type": {
    "id": 23,
    "name": "testss_312312sad31232s23"
  }
}
```

#### Получить список существующих предметов
GET `/api/items/`

Пример запроса:

`http://192.168.1.111:8000/api/items/`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDMiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.h03DSt2wnfm0vjldU-ravc2fd8X6y8tQAbj3N0YixbM"
}
```
Ответ:
```
{
  "items": [
    {
      "id": 20,
      "description": null,
      "type_id": 24,
      "owner_id": 40
    }
  ]
}
```

#### Получить список существующих предметов по идентификатору
GET `/api/items/{id}`

Пример запроса:

`http://192.168.1.111:8000/api/items/20`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDMiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.h03DSt2wnfm0vjldU-ravc2fd8X6y8tQAbj3N0YixbM"
}
```
Ответ:
```
{
  "item_type": {
    "id": 20,
    "description": null,
    "type_id": 24,
    "owner_id": 40
  }
}
```

#### Добавить новый тип предметов
POST `/api/items/types`

Пример запроса:

`http://192.168.1.111:8000/api/items/types`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDMiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.h03DSt2wnfm0vjldU-ravc2fd8X6y8tQAbj3N0YixbM",
	"data": {
		"name": "test_item"
	}
}
```
Ответ:
```
{
  "ok": "true"
}
```

### Предложения на продажу и покупку
#### Получить список предложений на покупку
GET `/api/orders/buy`

Пример запроса:

`http://192.168.1.111:8000/api/orders/buy`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDAiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.KB1cuzaozJcPeU23GfELeiFfXXUaJwt5OcI6fRO-QgQ"
}
```
Ответ:
```
{
  "ok": "true",
  "buy_orders": [
    {
      "id": 14,
      "owner": 40,
      "item": 20,
      "created": {
        "date": "2019-09-08 05:51:24.000000",
        "timezone_type": 3,
        "timezone": "UTC"
      },
      "type": "BUY"
    },
    {
      "id": 15,
      "owner": 40,
      "item": 21,
      "created": {
        "date": "2019-09-08 05:51:36.000000",
        "timezone_type": 3,
        "timezone": "UTC"
      },
      "type": "BUY"
    }
  ]
}
```

#### Добавить своё предложение на покупку
POST `/api/orders/buy`

Пример запроса:

`http://192.168.1.111:8000/api/orders/buy`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDAiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.KB1cuzaozJcPeU23GfELeiFfXXUaJwt5OcI6fRO-QgQ",
	"data" : {
		"item_id" : "21",
		"price" : "551"
	}
}
```
Ответ:
```
{
  "ok": "true",
  "order_id": 15
}
```

#### Получить список предложений на продажу
GET `/api/orders/sell`

Пример запроса:

`http://192.168.1.111:8000/api/orders/sell`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDAiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.KB1cuzaozJcPeU23GfELeiFfXXUaJwt5OcI6fRO-QgQ"
}
```
Ответ:
```
{
  "ok": "true",
  "sell_orders": [
    {
      "id": 16,
      "owner": 40,
      "item": 21,
      "created": {
        "date": "2019-09-08 05:52:26.000000",
        "timezone_type": 3,
        "timezone": "UTC"
      },
      "type": "SELL"
    },
    {
      "id": 17,
      "owner": 40,
      "item": 20,
      "created": {
        "date": "2019-09-08 06:15:50.000000",
        "timezone_type": 3,
        "timezone": "UTC"
      },
      "type": "SELL"
    }
  ]
}
```

#### Добавить своё предложение на продажу
POST `/api/orders/sell`

Пример запроса:

`http://192.168.1.111:8000/api/orders/sell`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDAiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.KB1cuzaozJcPeU23GfELeiFfXXUaJwt5OcI6fRO-QgQ",
	"data" : {
		"item_id" : "20",
		"price" : "551"
	}
}
```
Ответ:
```
{
  "ok": "true",
  "order_id": 17
}
```

#### Изменить существующее предложение
PUT `/api/orders/{id}`

Пример запроса:

`http://192.168.1.111:8000/api/orders/17`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDAiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.DHTTMC8yJlujeDHjXfuoOO6415IQVLcy-O5GL1GV3Do",
	"data" : {
		"price" : "221"
	}
}
```
Ответ:
```
{
  "ok": "true"
}
```

#### Отменить предложение (Предложение переходит в список отменённых. Удаления не происходит)
DELETE `/api/orders/{id}`

Пример запроса:

`http://192.168.1.111:8000/api/orders/17`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDAiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.DHTTMC8yJlujeDHjXfuoOO6415IQVLcy-O5GL1GV3Do"
}
```
Ответ:
```
{
  "ok": "true"
}
```

#### Продать товар по предложению
POST `/api/orders/{id}/sell`

Пример запроса:

`http://192.168.1.111:8000/api/orders/buy`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDAiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.KB1cuzaozJcPeU23GfELeiFfXXUaJwt5OcI6fRO-QgQ"
}
```
Ответ:
```
{
  "ok": "true"
}
```

#### Купить товар по предложению
POST `/api/orders/{id}/buy`

Пример запроса:

`http://192.168.1.111:8000/api/orders/buy`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDAiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.KB1cuzaozJcPeU23GfELeiFfXXUaJwt5OcI6fRO-QgQ"
}
```
Ответ:
```
{
  "ok": "true"
}
```


### Топ популярности
#### Список пользователей с сортировкой по количеству вещей, либо по сумме на балансе
GET `/api/top/users`

Пример запроса:

`http://192.168.1.111:8000/api/top/users`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDMiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.h03DSt2wnfm0vjldU-ravc2fd8X6y8tQAbj3N0YixbM",
		"filter" : "items"
}
```
Ответ:
```
{
  "ok": "true",
  "users": {
    "40": 2,
    "43": 0,
    "39": 0,
    "41": 0,
    "42": 0
  }
}
```

#### Список вещей по популярности
GET `/api/top/items`

Пример запроса:

`http://192.168.1.111:8000/api/top/items`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDMiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.h03DSt2wnfm0vjldU-ravc2fd8X6y8tQAbj3N0YixbM"
}
```
Ответ:
```
{
  "ok": "true",
  "items": [
    {
      "id": 20,
      "owner": 40,
      "sell count": 0
    },
    {
      "id": 21,
      "owner": 40,
      "sell count": 0
    }
  ]
}
```


### Изменение параметров пользователей
#### Получить информацию о пользователе 
GET `/api/users/{id}`

Пример запроса:

`http://192.168.1.111:8000/api/users/40`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDMiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.h03DSt2wnfm0vjldU-ravc2fd8X6y8tQAbj3N0YixbM"
}
```
Ответ:
```
{
  "name": "qwe",
  "balance": 0,
  "items": {}
}
```

#### Добавить предмет заданного типа пользователю
POST `/api/users/{id}/items`

Пример запроса:

`http://192.168.1.111:8000/api/users/40/items`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDMiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.h03DSt2wnfm0vjldU-ravc2fd8X6y8tQAbj3N0YixbM",
	"data" : {
		"item_type_id" : "23"
	}
}
```
Ответ:
```
{
  "ok": "true"
}
```

#### Получить историю действий пользователя
GET `/api/users/{id}/history`

Пример запроса:

`http://192.168.1.111:8000/api/users/40/history`

```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDMiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.h03DSt2wnfm0vjldU-ravc2fd8X6y8tQAbj3N0YixbM"
}
```
Ответ:
```
{
  "ok": "true",
  "orders": []
}
```


## Полезные команды
Doctrine scheme update: `vendor/bin/doctrine orm:schema-tool:update --force`

Run unit tests `./vendor/bin/phpunit --testsuite unit_tests` (Not yet implemented)

Composer update autoload `composer dump-autoload -o`