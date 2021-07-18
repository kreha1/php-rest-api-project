# REST API w PHP

Ponieważ pisałem to na Windowsie, niestety nie mogę przygotować projektu z dockerem. Wykorzystuję composer do zarządzania dependencies dla kilku drobnych utills.  


```
composer install
```

Żeby wstępnie zaludnić bazę:
```
php -S 127.0.0.1:8000 .\init_db.php
```

API do testowania:
```
php -S 127.0.0.1:8000 .\index.php
```

## Endpointy:

### Collections
```
GET /api/v1/posts       - wszystkie posty  
GET /api/v1/users       - wszyscy użytkownicy  
GET /api/v1/users/:id - wszystkie posty użytkownika  
```
### Account management
```
POST  /api/v1/account/create   - stwórz użytkowika  
PUT   /api/v1/account/password - ustaw nowe hasło  
PATCH /api/v1/account/activate - aktywuj użytkownika  
POST  /api/v1/account/auth     - sprawdź credentiale  
PATCH /api/v1/account/close    - wyłącz konto  
POST  /api/v1/account/delete   - wyłącz konto i usuń za 30 dni  
```  
### Content management  
```
PUT    posts/new         - stwórz nowy post  
POST   posts/edit/:id    - edytuj istniejący post
POST   posts/hide/:id    - ukryj istniejący post 
POST   posts/show/:id    - odkry istniejący post
DELETE posts/delete/:id  - usuń post
```

Oryginalnie planowałem zaimplementować JWT, ale za długo mi się zeszło z rozpracowaniem routingu, dlatego nie ma żadnej weryfikacji uprawnień.
