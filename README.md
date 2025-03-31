## 📌 Opis projektu
**Warsztat Samochodowy** to aplikacja webowa umożliwiająca zarządzanie klientami, pojazdami oraz zleceniami serwisowymi. Projekt wykorzystuje **PHP**, **Oracle SQL** oraz **XAMPP**, oferując intuicyjny interfejs i funkcjonalności ułatwiające prowadzenie warsztatu.

## 🛠️ Technologie
- **PHP** – logika aplikacji  
- **Oracle SQL** – baza danych  
- **XAMPP** – lokalne środowisko serwera  
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
│── /css           # Style CSS
│── /js            # Skrypty JavaScript
│── /images        # Obrazy i logo
│── /sql           # Pliki SQL do bazy danych
│── config.php     # Konfiguracja połączenia z bazą danych
│── index.php      # Strona główna
│── klient_add.php # Dodawanie klientów
│── pojazd_add.php # Dodawanie pojazdów
│── zlecenie_add.php # Tworzenie zleceń
│── README.md      # Dokumentacja projektu
```

## 📌 Funkcjonalności
✅ **Dodawanie i przeglądanie klientów**  
✅ **Rejestrowanie pojazdów**  
✅ **Tworzenie zleceń serwisowych**  
✅ **Podgląd statystyk**  

## 📜 Licencja
Projekt dostępny na licencji **MIT** – możesz go dowolnie modyfikować i rozwijać. 🚀  

---

Czy chcesz dodać coś jeszcze, np. screeny interfejsu? 🎨
