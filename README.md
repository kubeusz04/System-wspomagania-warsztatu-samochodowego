# 🚗 Warsztat Samochodowy 

## 📌 Opis projektu  
**Warsztat Samochodowy** to aplikacja webowa umożliwiająca zarządzanie klientami, pojazdami oraz zleceniami serwisowymi. Projekt został stworzony w **PHP**, korzysta z **Oracle SQL** jako bazy danych i działa lokalnie na **XAMPP**.  

## 🛠️ Technologie  
- **PHP** – logika aplikacji  
- **Oracle SQL** – baza danych  
- **XAMPP** – lokalny serwer  
- **Bootstrap** – responsywny design  
- **HTML/CSS** – frontend  

## 📥 Instalacja  

### 1️⃣ Pobierz repozytorium  
Pobierz projekt i umieść go w katalogu `htdocs` XAMPP.  

### 2️⃣ Skonfiguruj bazę danych  
- Zainstaluj **Oracle Database XE** (lub użyj istniejącej bazy).  
- Zaimportuj strukturę tabel z pliku `baza.sql`.  
- Skonfiguruj połączenie w `config.php` (dostosuj dane logowania do bazy).  

### 3️⃣ Skonfiguruj PHP i XAMPP  
- **Zainstaluj Oracle Instant Client** (wersja **21+**) z:  
  👉 [https://www.oracle.com/database/technologies/instant-client.html](https://www.oracle.com/database/technologies/instant-client.html)  
  (Dodaj ścieżkę do Instant Client w zmiennych środowiskowych systemu).  

- **Włącz rozszerzenie PHP dla Oracle**:  
  - W pliku `php.ini` odszukaj i odkomentuj:  
    ```
    extension=oci8_19
    ```
  - Zrestartuj Apache w XAMPP.  

### 4️⃣ Uruchom serwer  
- Włącz **Apache** i **Oracle Database**.  
- Otwórz przeglądarkę i wpisz:  
  ```
  http://localhost/warsztat/
  ```  

## 📂 Struktura katalogów  
```
/warsztat
│── config.php           # Konfiguracja bazy danych
│── header.php           # Nagłówek strony (menu)
│── footer.php           # Stopka strony
│── index.php            # Strona główna (dashboard)
│── klient_add.php       # Dodawanie klientów
│── klient_delete.php    # Usuwanie klientów
│── pojazd_add.php       # Dodawanie pojazdów
│── pojazd_delete.php    # Usuwanie pojazdów
│── zlecenie_add.php     # Tworzenie zleceń
│── zlecenie_delete.php  # Usuwanie zleceń
│── zlecenie_edit.php    # Edycja statusu zleceń
│── README.md            # Dokumentacja projektu
│── baza.sql             # Baza danych
```

## 📌 Funkcjonalności  
✅ **Dodawanie i usuwanie klientów**  
✅ **Rejestrowanie i usuwanie pojazdów**  
✅ **Tworzenie, edytowanie i usuwanie zleceń**  
✅ **Wyświetlanie statystyk na stronie głównej**  

## 📜 Licencja  
Projekt dostępny na licencji **MIT** – możesz go dowolnie modyfikować i rozwijać. 🚀  
