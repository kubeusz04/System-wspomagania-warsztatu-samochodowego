
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
1. **Pobierz repozytorium:**
   ```bash
   git clone https://github.com/twoj-user/twoj-projekt.git
   cd twoj-projekt
   ```
2. **Skonfiguruj bazę danych:**
   - Utwórz bazę danych w **Oracle SQL**.
   - Zaimportuj strukturę tabel z pliku `database.sql`.
   - Skonfiguruj połączenie w `config.php`.

3. **Uruchom XAMPP i Apache:**
   - Skopiuj pliki do folderu `htdocs` w katalogu XAMPP.
   - Włącz Apache i Oracle.

4. **Uruchom aplikację w przeglądarce:**
   ```
   http://localhost/twoj-projekt/
   ```

## 📂 Struktura katalogów
```
/twoj-projekt
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
```

## 📌 Funkcjonalności
✅ **Dodawanie i usuwanie klientów**  
✅ **Rejestrowanie i usuwanie pojazdów**  
✅ **Tworzenie, edytowanie i usuwanie zleceń**  
✅ **Wyświetlanie statystyk na stronie głównej**  

## 📜 Licencja
Projekt dostępny na licencji **MIT** – możesz go dowolnie modyfikować i rozwijać. 🚀  
