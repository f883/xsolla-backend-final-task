# Документация

## Уровни доступа
#### Гость
Без авторизации имеет доступ к следающим методам:
* '/api/exchange',
* '/api/top/users',

#### Пользователь
Требуется авторизация.
Имеет доступ ко всем методам, кроме тех, которые доступны только администратору.

#### Администратор
Требуется авторизация.
Помимо доступа к методам пользователя может использовать следующие методы:
* Пополнить баланс пользователя
* Списать с баланса пользователя
* Создать тип предмета
* Начислить предмет пользователю
* Изменить комиссию на торговой площадке
* Получить баланс торговой площадки
* Получить выручку за определенный период


## Авторизация
Вход в приложение осуществляется по access_token. Он должен быть указан в теле запроса.
Для получения нового access_token используется refresh_token. 
Получение токенов происходит после авторизации через пару логин-пароль.
Пример запроса 

POST `/api/orders`
```
{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYWNjZXNzX3Rva2VuIiwidXNlcklkIjoiNDAiLCJpc3MiOiJodHRwOlwvXC9leGFtcGxlLm9yZyIsImF1ZCI6Imh0dHA6XC9cL2V4YW1wbGUuY29tIiwiaWF0IjoxNTY3NjQxNjAwLCJuYmYiOjE1Njc2NDE2MDAsImV4cCI6MTU2NzcyODAwMH0.82CKuNOA3RG3FktNcxxPqde6CzKK3Hc0ON7UywyGy7I",
	"data":{
		"item_id" : "20",
		"price" : "28"
	}
}
```

Access_token не требуется для запросов по следующим путям:
* '/api/auth/register',
* '/api/auth/login',
* '/api/exchange',
* '/api/top/users',
* '/api/auth/updatetoken' (Требуется refresh_token)


#### Регистрация
POST `/api/auth/register`
Тело запроса должно содержать name и password.

#### Вход в систему (получение токенов)
POST `/api/auth/login`

#### Выход из системы (инвалидация всех токенов)
POST `/api/auth/logout`

#### Обновление access_token
POST `/api/auth/updatetoken`


## Биржа
#### Получить статус биржи
GET `/api/exchange/`

#### Установить налог на покупки
POST `/api/exchange/fee`

#### Получить баланс биржи
GET `/api/exchange/balance`

#### Обновление access_token
GET `/api/exchange/earn`

#### Обновление access_token
PUT `/api/exchange/deposit/user/{id}`

#### Обновление access_token
PUT `/api/exchange/withdraw/user/{id}`


## Предметы
#### Получить список существующих типов предметов
GET `/api/items/types`

#### Получить описание типа предмета по идентификатору
GET `/api/items/types/{id}`

#### Получить список существующих предметов
GET `/api/items/`

#### Получить список существующих предметов по идентификатору
GET `/api/items/{id}`

#### Добавить новый тип предметов
POST `/api/items/types`


## Предложения на продажу и покупку
#### Получить список предложений на покупку
GET `/api/orders/buy`

#### Добавить своё предложение на покупку
POST `/api/orders/buy`

#### Получить список предложений на продажу
GET `/api/orders/sell`

#### Добавить своё предложение на продажу
POST `/api/orders/sell`

#### Изменить существующее предложение
PUT `/api/orders/{id}`

#### Отменить предложение (Предложение переходит в список отменённых. Удаления не происходит)
DELETE `/api/orders/{id}`


## Топ популярности
#### Список пользователей с сортировкой по количеству вещей, либо по сумме на балансе
GET `/api/top/users`

#### Список вещей по популярности
GET `/api/top/items`


## Изменение параметров пользователей
#### Получить информацию о пользователе 
GET `/api/users/{id}`

#### Добавить предмет заданного типа пользователю
POST `/api/users/{id}/items`

#### Получить историю действий пользователя
GET `/api/users/{id}/history`