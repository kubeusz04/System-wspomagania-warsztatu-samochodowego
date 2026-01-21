# 🚗💼 System Wspomagania Warsztatu Samochodowego - CRM Edition

## 📌 Opis projektu

**System Wspomagania Warsztatu Samochodowego** to zaawansowana aplikacja webowa typu **CRM (Customer Relationship Management)** z modułem serwisowym. Projekt został stworzony w **PHP**, korzysta z **Oracle SQL** jako bazy danych i działa lokalnie na **XAMPP**.

### Główne funkcje:

**🎯 Moduł CRM:**
- ✅ **Kontakty** - zarządzanie bazą kontaktów i firm
- ✅ **Leady** - pozyskiwanie i kwalifikacja potencjalnych klientów
- ✅ **Szanse sprzedaży (Pipeline)** - śledzenie możliwości biznesowych
- ✅ **Aktywności** - planowanie zadań, spotkań, rozmów telefonicznych
- ✅ **System RBAC** - kontrola dostępu oparta na rolach (Admin, Handlowiec, Serwisant)

**🔧 Moduł Serwisowy:**
- ✅ **Klienci** - zarządzanie klientami warsztatu
- ✅ **Pojazdy** - rejestr pojazdów klientów
- ✅ **Zlecenia** - obsługa zleceń naprawczych

## 🛠️ Technologie

- **PHP** – logika aplikacji z systemem uwierzytelniania
- **Oracle SQL** – baza danych z zaawansowanymi indeksami i słownikami
- **XAMPP** – lokalny serwer
- **Bootstrap 4** – responsywny design
- **Font Awesome** – ikony
- **HTML/CSS** – frontend

## 📥 Instalacja

### 1️⃣ Pobierz repozytorium

Pobierz projekt i umieść go w katalogu `htdocs` XAMPP.

```bash
git clone https://github.com/kubeusz04/System-wspomagania-warsztatu-samochodowego.git
cd System-wspomagania-warsztatu-samochodowego
```

### 2️⃣ Skonfiguruj bazę danych Oracle

#### A. Zainstaluj Oracle Database XE

- Pobierz i zainstaluj **Oracle Database XE** (wersja 18c lub 21c)
- Utwórz pluggable database (XEPDB1) jeśli nie istnieje

#### B. Zaimportuj strukturę bazy danych

Połącz się z bazą jako użytkownik SYSTEM:

```bash
sqlplus SYSTEM/hasło@localhost/XEPDB1
```

Wykonaj skrypty SQL w kolejności:

```sql
-- 1. Utwórz schemat i tabele CRM
@database/01_schema.sql

-- 2. Załaduj dane słownikowe i użytkowników testowych
@database/02_seed_dict.sql

-- 3. (Opcjonalnie) Zmigruj istniejące dane i dodaj przykłady
@database/03_migrate_existing.sql
```

**Weryfikacja instalacji:**

```sql
-- Sprawdź liczbę tabel
SELECT COUNT(*) FROM USER_TABLES;

-- Sprawdź użytkowników
SELECT USERNAME, ROLE_CODE FROM USERS;
```

### 3️⃣ Skonfiguruj PHP i XAMPP

#### A. Zainstaluj Oracle Instant Client

- Pobierz **Oracle Instant Client** (wersja **21+**) z:  
  👉 [https://www.oracle.com/database/technologies/instant-client.html](https://www.oracle.com/database/technologies/instant-client.html)
- Rozpakuj i dodaj ścieżkę do Instant Client w zmiennych środowiskowych systemu

#### B. Włącz rozszerzenie PHP dla Oracle

W pliku `php.ini` (znajdź w XAMPP Control Panel → Config):

```ini
extension=oci8_19
```

Zrestartuj Apache w XAMPP.

#### C. Skonfiguruj połączenie z bazą

Edytuj plik `config.php` i dostosuj dane połączenia:

```php
$conn = oci_connect('SYSTEM', 'twoje_haslo', 'localhost/XEPDB1');
```

### 4️⃣ Uruchom aplikację

- Włącz **Apache** w XAMPP
- Upewnij się, że **Oracle Database** działa
- Otwórz przeglądarkę i wpisz:

```
http://localhost/warsztat/login.php
```

### 5️⃣ Zaloguj się

Użyj jednego z kont testowych:

| Username | Hasło | Rola | Dostęp |
|----------|-------|------|--------|
| admin | admin123 | Administrator | Pełny dostęp |
| sprzedawca | admin123 | Handlowiec | Moduł CRM |
| serwisant | admin123 | Serwisant | Moduł Serwisowy |

**⚠️ WAŻNE:** Po pierwszym logowaniu zmień hasła domyślne!

## 📂 Struktura projektu

```
/warsztat
│
├── database/                    # 📊 Skrypty bazy danych
│   ├── 01_schema.sql           # Schemat tabel CRM i serwisowych
│   ├── 02_seed_dict.sql        # Dane słownikowe i użytkownicy
│   ├── 03_migrate_existing.sql # Migracja i przykładowe dane
│   └── README_DB.md            # Dokumentacja bazy danych
│
├── config.php                   # Konfiguracja połączenia z bazą
├── auth.php                     # System uwierzytelniania i RBAC
├── header.php                   # Nagłówek strony (menu z kontrolą dostępu)
├── footer.php                   # Stopka strony
├── login.php                    # Strona logowania
├── logout.php                   # Wylogowanie
├── index.php                    # Dashboard z statystykami CRM i Serwisu
│
├── contacts_list.php            # 👥 Lista kontaktów
├── contact_add.php              # Dodawanie kontaktu
├── contact_edit.php             # Edycja kontaktu
├── contact_delete.php           # Usuwanie kontaktu
│
├── leads_list.php               # 🎯 Lista leadów
├── lead_add.php                 # Dodawanie leadu
├── lead_edit.php                # Edycja leadu
├── lead_delete.php              # Usuwanie leadu
│
├── opportunities_list.php       # 💰 Lista szans sprzedaży
├── opportunity_add.php          # Dodawanie szansy
├── opportunity_edit.php         # Edycja szansy
├── opportunity_delete.php       # Usuwanie szansy
│
├── activities_list.php          # 📅 Lista aktywności
├── activity_add.php             # Dodawanie aktywności
├── activity_edit.php            # Edycja aktywności
├── activity_delete.php          # Usuwanie aktywności
│
├── klient_add.php               # 🔧 Moduł Serwisowy - Klienci
├── klient_delete.php
├── pojazd_add.php               # Pojazdy
├── pojazd_delete.php
├── zlecenie_add.php             # Zlecenia
├── zlecenie_edit.php
├── zlecenie_delete.php
│
└── README.md                    # Ten plik
```

## 📊 Baza Danych - Przegląd

### Tabele Słownikowe (Dictionary Tables)

System wykorzystuje 9 tabel słownikowych z **unikalnymi indeksami na kodach**:

| Tabela | Opis | Przykłady Kodów |
|--------|------|-----------------|
| DICT_USER_STATUS | Statusy użytkowników | ACTIVE, INACTIVE, BLOCKED |
| DICT_USER_ROLE | Role RBAC | ADMIN, SALES, USER, SERVICE |
| DICT_LEAD_SOURCE | Źródła leadów | WEBSITE, PHONE, EMAIL, REFERRAL |
| DICT_LEAD_STATUS | Statusy leadów | NEW, CONTACTED, QUALIFIED, CONVERTED |
| DICT_OPPORTUNITY_STAGE | Etapy pipeline | PROSPECTING, PROPOSAL, NEGOTIATION, CLOSED_WON |
| DICT_ACTIVITY_TYPE | Typy aktywności | TASK, MEETING, CALL, EMAIL |
| DICT_ACTIVITY_STATUS | Statusy aktywności | PLANNED, IN_PROGRESS, COMPLETED, CANCELLED |
| DICT_PRIORITY | Priorytety | LOW, MEDIUM, HIGH, URGENT |
| DICT_ORDER_STATUS | Statusy zleceń | PENDING, IN_PROGRESS, COMPLETED |

### Tabele CRM

- **USERS** - Użytkownicy z rolami RBAC
- **ACCOUNTS** - Firmy/konta (opcjonalne)
- **CONTACTS** - Kontakty/osoby
- **LEADS** - Leady (potencjalni klienci)
- **OPPORTUNITIES** - Szanse sprzedaży
- **ACTIVITIES** - Aktywności (zadania, spotkania, rozmowy)
- **NOTES** - Notatki
- **ATTACHMENTS** - Załączniki

### Indeksy

System zawiera **ponad 40 indeksów** dla optymalizacji wydajności:

- **Indeksy na kluczach obcych (FK)** - dla wszystkich relacji między tabelami
- **Indeksy złożone** - dla typowych zapytań:
  - `IDX_LEAD_OWNER_STATUS` - lista leadów po właścicielu i statusie
  - `IDX_OPP_OWNER_STAGE` - lista szans po właścicielu i etapie
  - `IDX_ACTIVITY_OWNER_STATUS_DUE` - kalendarz aktywności
  - `IDX_CONTACT_NAME` - wyszukiwanie po nazwisku i imieniu

Szczegóły w [database/README_DB.md](database/README_DB.md)

## 🔐 System RBAC (Role-Based Access Control)

### Role w systemie:

| Rola | Kod | Uprawnienia |
|------|-----|-------------|
| **Administrator** | ADMIN | Pełny dostęp do wszystkich modułów |
| **Handlowiec** | SALES | Dostęp do modułu CRM (odczyt, zapis, usuwanie) |
| **Użytkownik** | USER | Podstawowy dostęp (tylko odczyt CRM) |
| **Serwisant** | SERVICE | Dostęp do modułu serwisowego |

### Kontrola dostępu w kodzie:

```php
// Sprawdź czy użytkownik jest zalogowany
require_login();

// Sprawdź uprawnienie
require_permission('crm.write');

// Sprawdź rolę
require_role('ADMIN');

// Warunkowe wyświetlanie
if (has_permission('crm.delete')) {
    // Pokaż przycisk usuń
}
```

## 📌 Funkcjonalności

### Moduł CRM 💼

✅ **Kontakty**
- Lista z filtrowaniem (szukaj, właściciel)
- Sortowanie (nazwisko, imię, email, data utworzenia)
- Paginacja (20 rekordów na stronę)
- Dodawanie/Edycja/Usuwanie (z kontrolą uprawnień)

✅ **Leady**
- Filtrowanie po statusie (Nowy, Skontaktowany, Zakwalifikowany, itp.)
- Rating (Hot/Warm/Cold)
- Źródło pozyskania
- Konwersja do kontaktu

✅ **Szanse sprzedaży**
- Pipeline z etapami (Prospecting → Closed Won/Lost)
- Prawdopodobieństwo zamknięcia (%)
- Wartość szansy
- Przewidywana data zamknięcia

✅ **Aktywności**
- Typy: Zadanie, Spotkanie, Rozmowa, Email
- Statusy: Zaplanowana, W trakcie, Zakończona, Anulowana
- Priorytety: Niski, Średni, Wysoki, Pilny
- Termin wykonania

### Moduł Serwisowy 🔧

✅ **Klienci** - dodawanie i usuwanie klientów warsztatu  
✅ **Pojazdy** - rejestrowanie pojazdów z walidacją roku produkcji  
✅ **Zlecenia** - tworzenie, edytowanie i usuwanie zleceń naprawy  
✅ **Statystyki** - wyświetlanie liczby klientów, pojazdów i zleceń

### Dashboard 📊

- **Statystyki CRM** - liczba kontaktów, aktywnych leadów, otwartych szans, otwartych aktywności
- **Statystyki Serwisu** - liczba klientów, pojazdów, zleceń
- **Szybkie akcje** - przyciski do dodawania nowych rekordów

## 🔒 Bezpieczeństwo

✅ **Prepared Statements** - wszystkie zapytania SQL używają bindowania parametrów  
✅ **RBAC** - kontrola dostępu oparta na rolach  
✅ **Sesje PHP** - bezpieczne zarządzanie sesjami  
✅ **Hashowanie haseł** - SHA-256 (zalecane: bcrypt w produkcji)  
✅ **XSS Protection** - htmlspecialchars() na wszystkich wyświetlanych danych

⚠️ **Zalecenia dla produkcji:**
- Zmień hasła domyślne użytkowników
- Użyj dedykowanego użytkownika bazy danych (nie SYSTEM)
- Rozważ zmianę hashowania na bcrypt/Argon2
- Włącz HTTPS
- Skonfiguruj backup bazy danych

## 🚀 Rozbudowa

### Możliwe rozszerzenia:

- 📧 **Email Integration** - wysyłanie emaili z CRM
- 📄 **Generowanie PDF** - oferty, faktury, umowy
- 📈 **Raporty i Analytics** - wykresy sprzedaży, konwersji
- 🔔 **Powiadomienia** - przypomnienia o aktywnościach
- 📱 **API REST** - integracja z aplikacjami mobilnymi
- 🌐 **Multi-tenancy** - obsługa wielu firm
- 📊 **Dashboard BI** - zaawansowane statystyki biznesowe

## 📜 Licencja

Projekt dostępny na licencji **MIT** – możesz go dowolnie modyfikować i rozwijać. 🚀

## 👨‍💻 Autor

**Zawadzki Kuba** (nr albumu - 12500)

## 📞 Kontakt

Jeśli masz pytania lub sugestie dotyczące projektu, otwórz Issue na GitHubie.

---

**Data aktualizacji:** 2026-01-21  
**Wersja:** 2.0 (CRM Edition)  
**Status:** Produkcyjny ✅
  
