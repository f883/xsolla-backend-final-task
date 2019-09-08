# Документация API
Уровни доступа
Авторизация
Биржа
Предметы
Предложения на продажу и покупку
Топ популярности
Изменение параметров пользователей

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


### Регистрация
POST `/api/auth/register`
Тело запроса должно содержать name и password.
Пример запроса:
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
  "user_id": 43
}
```

### Вход в систему (получение токенов)
POST `/api/auth/login`

Пример запроса:
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

### Выход из системы (инвалидация всех токенов)
POST `/api/auth/logout`

Пример запроса:
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

### Обновление access_token
POST `/api/auth/updatetoken`

Пример запроса:
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


## Биржа
### Получить статус биржи
GET `/api/exchange`

Пример запроса:
```
{
"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDIiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3OTAwODAwLCJuYmYiOjE1Njc5MDA4MDAsImV4cCI6MTU2Nzk4NzIwMH0.fIrt4VKRCBVQBdanIw_Y79vmf6KG7cg41wtERk_PWJA"
}
```
Ответ:
```
{
  "ok": "true",
  "data": {
    "fee": 0.05,
    "users_count": 4,
    "orders_count": 4
  }
}
```

### Установить налог на покупки
POST `/api/exchange/fee`

Пример запроса:
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

### Получить баланс биржи
GET `/api/exchange/balance`

Пример запроса:
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

### Получить прибыль биржи за заданный период времени
GET `/api/exchange/earn`

Пример запроса:
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

### Добавить деньги на баланс пользователя
PUT `/api/exchange/deposit/user/{id}`

Пример запроса:
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

### Снять деньги с баланса пользователя
PUT `/api/exchange/withdraw/user/{id}`

Пример запроса:
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

## Предметы
### Получить список существующих типов предметов
GET `/api/items/types`

Пример запроса:
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

### Получить описание типа предмета по идентификатору
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

### Получить список существующих предметов
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

### Получить список существующих предметов по идентификатору
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

### Добавить новый тип предметов
POST `/api/items/types`

Пример запроса:
```

```
Ответ:
```

```

## Предложения на продажу и покупку
### Получить список предложений на покупку
GET `/api/orders/buy`

Пример запроса:
```

```
Ответ:
```

```

### Добавить своё предложение на покупку
POST `/api/orders/buy`

Пример запроса:
```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDAiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3NjQxNjAwLCJuYmYiOjE1Njc2NDE2MDAsImV4cCI6MTU2NzcyODAwMH0.82CKuNOA3RG3FktNcxxPqde6CzKK3Hc0ON7UywyGy7I",
	"data":{
		"item_id" : "20",
		"price" : "28"
	}
}
```
Ответ:
```

```

### Получить список предложений на продажу
GET `/api/orders/sell`

Пример запроса:
```

```
Ответ:
```

```

### Добавить своё предложение на продажу
POST `/api/orders/sell`

Пример запроса:
```

```
Ответ:
```

```

### Изменить существующее предложение
PUT `/api/orders/{id}`

Пример запроса:
```

```
Ответ:
```

```

### Отменить предложение (Предложение переходит в список отменённых. Удаления не происходит)
DELETE `/api/orders/{id}`

Пример запроса:
```

```
Ответ:
```

```


## Топ популярности
### Список пользователей с сортировкой по количеству вещей, либо по сумме на балансе
GET `/api/top/users`

Пример запроса:
```

```
Ответ:
```

```

### Список вещей по популярности
GET `/api/top/items`

Пример запроса:
```

```
Ответ:
```

```


## Изменение параметров пользователей
### Получить информацию о пользователе 
GET `/api/users/{id}`

Пример запроса:
```

```
Ответ:
```

```

### Добавить предмет заданного типа пользователю
POST `/api/users/{id}/items`

Пример запроса:
```

```
Ответ:
```

```

### Получить историю действий пользователя
GET `/api/users/{id}/history`

Пример запроса:
```

```
Ответ:
```

```