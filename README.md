# Итоговое задание по потоку backend летней школы Xsolla 2019

## Документация
Документация представлена [здесь](DOCS.md)

## TODO
* Дописать юнит тесты


## Задание 
Необходимо разработать систему “Торговая площадка виртуальных предметов”.
В рамках данной системы, торговая площадка выступает в качестве посредника между покупателем и продавцом, за что берет процент с каждой торговой операции. 
Процент отчисляется с суммы продажи, к примеру: покупатель оплачивает 100% стоимости товара, продавец получает 95%, на баланс биржи начисляется 5%.  
Процент комиссии может настраиваться. 
Каждый пользователь может получить информацию только о своих операциях, администратор биржи может получить информацию о любом пользователе.

### Действующие лица:
* Пользователь
* Администратор биржи

### Определения: 
* Инвентарь - это список игровых предметов, которые на данный момент принадлежат пользователю.
* Товар - игровой предмет, который выставлен на продажу.
* Ордер - запрос на покупку или продажу определенного типа предмета.
* Комиссия - процент с продажи товара, который начисляется на баланс торговой площадки.

### Пример: 
Администратор биржи установил комиссию 5%. На бирже зарегистрировалось три пользователя Вася, Петя и Семен. Администратор биржи создал предмет с типом “Пистолет” и начислил его Васе в количестве двух штук, затем начислил 100 рублей Пете и 90 рублей Семену. У Васи в инвентаре два уникальных предмета с нулевым значением счетчика перепродаж. Вася создал ордер на продажу одного пистолета с ценой 100 рублей, после чего Петя купил этот пистолет, заплатив 100 рублей, Вася после продажи получил 95 рублей, а на баланс биржи было начислено 5 рублей. После продажи на купленном предмете увеличился счетчик перепродажи на единицу. 
Семен решил купить такой же пистолет, но у него было только 90 рублей, поэтому он создал ордер на покупку со стоимостью 90 рублей. 
Петя решил заработать на перепродаже и выставил свой пистолет за 110 рублей, но так как цена ордера Семена на покупку была меньше, то оба ордера остались открытыми.
После чего Вася решил продать свой оставшийся пистолет за 85 рублей и создал ордер на продажу, так как стоимость ордера на продажу Васи меньше стоимости ордера на покупку Семена, то оба ордера автоматически закрылись и Семен получил пистолет заплатив 85 рублей, Вася получил 80.75 рублей, на баланс биржи зачислено 4.25 рублей. 


### Условия:
* Товар можно перепродать только через 24 часа с момента его покупки.
* У товара должен быть счетчик, который отображает информацию о количестве его перепродаж.
* Ордер может содержать в себе только один предмет.

### Общие методы: 
* Авторизация
* Выход из системы
* Получить список предметов
* Получить список типов предметов
* Получить текущее состояние биржи (комиссия, количество предметов, количество ордеров)
* Самые продаваемые игровые предметы за определенный период
* Топ пользователей за определенное время (по количеству денег, по количеству товаров)
* Получить информацию о пользователе (имя, баланс, инвентарь)
* Обновить токен

### Методы администратора биржи:
* Пополнить баланс пользователя
* Списать с баланса пользователя
* Создать тип предмета
* Начислить предмет пользователю
* Изменить комиссию на торговой площадке
* Получить баланс торговой площадки
* Получить выручку за определенный период

### Методы пользователя:
* Регистрация
* Получить список ордеров на покупку (фильтры по предмету, по пользователю)
* Получить список ордеров на продажу (фильтры по предмету, по пользователю)
* Получить подробную информацию о товаре
* Купить товар
* Создать ордера на продажу
* Создать ордера на покупку
* Отменить ордер
* Обновить ордер
* Получить историю действий пользователя (фильтры по типу ордера)