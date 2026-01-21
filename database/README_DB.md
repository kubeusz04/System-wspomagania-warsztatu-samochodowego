# 📊 Dokumentacja Bazy Danych CRM

## Przegląd

System Wspomagania Warsztatu Samochodowego wersja 2.0 został rozszerzony o pełnoprawny moduł CRM (Customer Relationship Management). Baza danych Oracle zawiera:

- **Tabele słownikowe (Dictionary Tables)** - referencyjne dane z unikalnymi kodami
- **Tabele CRM** - kontakty, leady, szanse sprzedaży, aktywności, notatki
- **Tabele serwisowe** - zachowane z oryginalnego systemu warsztatowego
- **System RBAC** - role i uprawnienia użytkowników

## Struktura Plików SQL

### 1. `01_schema.sql`
Główny schemat bazy danych zawierający:
- Definicje wszystkich tabel
- Klucze główne i obce (PRIMARY KEY, FOREIGN KEY)
- Indeksy (pojedyncze i złożone)
- Ograniczenia CHECK
- Triggery

**Kolejność wykonania:** Najpierw

### 2. `02_seed_dict.sql`
Dane początkowe dla słowników:
- Statusy i role użytkowników
- Źródła i statusy leadów
- Etapy pipeline szans sprzedaży
- Typy i statusy aktywności
- Priorytety
- Statusy zleceń serwisowych
- Testowi użytkownicy (admin, sprzedawca, serwisant)

**Kolejność wykonania:** Po 01_schema.sql

### 3. `03_migrate_existing.sql`
Skrypt migracji i przykładowe dane:
- Migracja istniejących klientów do CONTACTS (opcjonalne)
- Mapowanie starych statusów zleceń na nowe
- Przykładowe dane testowe CRM
- Weryfikacja migracji

**Kolejność wykonania:** Po 02_seed_dict.sql (opcjonalnie)

## Tabele Słownikowe

Wszystkie tabele słownikowe mają wspólną strukturę:
- **CODE** (VARCHAR2) - unikalny kod, klucz główny
- **NAME** (VARCHAR2) - nazwa wyświetlana
- **DESCRIPTION** (VARCHAR2) - opis
- Dodatkowe pola specyficzne dla słownika

### Unikalne Indeksy na Kodach

Każda tabela słownikowa ma **unikalny indeks** na kolumnie CODE:

| Tabela | Indeks | Kolumna |
|--------|--------|---------|
| DICT_USER_STATUS | IDX_USER_STATUS_CODE | CODE |
| DICT_USER_ROLE | IDX_USER_ROLE_CODE | CODE |
| DICT_LEAD_SOURCE | IDX_LEAD_SOURCE_CODE | CODE |
| DICT_LEAD_STATUS | IDX_LEAD_STATUS_CODE | CODE |
| DICT_OPPORTUNITY_STAGE | IDX_OPP_STAGE_CODE | CODE |
| DICT_ACTIVITY_TYPE | IDX_ACTIVITY_TYPE_CODE | CODE |
| DICT_ACTIVITY_STATUS | IDX_ACTIVITY_STATUS_CODE | CODE |
| DICT_PRIORITY | IDX_PRIORITY_CODE | CODE |
| DICT_ORDER_STATUS | IDX_ORDER_STATUS_CODE | CODE |

### Lista Słowników

#### 1. DICT_USER_STATUS
Statusy użytkowników w systemie
- `ACTIVE` - Aktywny
- `INACTIVE` - Nieaktywny
- `BLOCKED` - Zablokowany

#### 2. DICT_USER_ROLE
Role RBAC (kontrola dostępu)
- `ADMIN` - Administrator (pełne uprawnienia)
- `SALES` - Handlowiec (CRM)
- `USER` - Użytkownik (podstawowe uprawnienia)
- `SERVICE` - Serwisant (moduł serwisowy)

#### 3. DICT_LEAD_SOURCE
Źródła pozyskania leadów
- `WEBSITE` - Strona WWW
- `PHONE` - Telefon
- `EMAIL` - Email
- `REFERRAL` - Polecenie
- `SOCIAL` - Media społecznościowe
- `ADVERTISING` - Reklama
- `TRADE_SHOW` - Targi
- `OTHER` - Inne

#### 4. DICT_LEAD_STATUS
Statusy leadów
- `NEW` - Nowy
- `CONTACTED` - Skontaktowany
- `QUALIFIED` - Zakwalifikowany
- `CONVERTED` - Przekonwertowany
- `UNQUALIFIED` - Niezakwalifikowany
- `LOST` - Stracony

#### 5. DICT_OPPORTUNITY_STAGE
Etapy pipeline sprzedaży (z prawdopodobieństwem zamknięcia)
- `PROSPECTING` - Prospecting (10%)
- `QUALIFICATION` - Kwalifikacja (20%)
- `NEEDS_ANALYSIS` - Analiza potrzeb (30%)
- `PROPOSAL` - Oferta (50%)
- `NEGOTIATION` - Negocjacje (70%)
- `CLOSED_WON` - Wygrana (100%)
- `CLOSED_LOST` - Stracona (0%)

#### 6. DICT_ACTIVITY_TYPE
Typy aktywności CRM
- `TASK` - Zadanie
- `MEETING` - Spotkanie
- `CALL` - Rozmowa telefoniczna
- `EMAIL` - Email

#### 7. DICT_ACTIVITY_STATUS
Statusy aktywności
- `PLANNED` - Zaplanowana
- `IN_PROGRESS` - W trakcie
- `COMPLETED` - Zakończona
- `CANCELLED` - Anulowana

#### 8. DICT_PRIORITY
Priorytety (dla aktywności, zadań)
- `LOW` - Niski (poziom 1)
- `MEDIUM` - Średni (poziom 2)
- `HIGH` - Wysoki (poziom 3)
- `URGENT` - Pilny (poziom 4)

#### 9. DICT_ORDER_STATUS
Statusy zleceń serwisowych
- `PENDING` - Oczekuje
- `IN_PROGRESS` - W trakcie
- `COMPLETED` - Zakończone
- `CANCELLED` - Anulowane

## Tabele CRM

### USERS
Użytkownicy systemu z rolami RBAC

**Kolumny:**
- ID_USER (PK) - klucz główny
- USERNAME - nazwa użytkownika (unikalna)
- PASSWORD_HASH - zaszyfrowane hasło (SHA-256)
- EMAIL - adres email (unikalny)
- FIRST_NAME, LAST_NAME - imię i nazwisko
- ROLE_CODE (FK) - rola użytkownika
- STATUS_CODE (FK) - status użytkownika
- CREATED_AT, UPDATED_AT, LAST_LOGIN - znaczniki czasowe

**Indeksy:**
- IDX_USER_ROLE (ROLE_CODE)
- IDX_USER_STATUS (STATUS_CODE)
- IDX_USER_EMAIL (EMAIL)

### ACCOUNTS
Firmy/Konta klientów (opcjonalne)

**Kolumny:**
- ID_ACCOUNT (PK)
- ACCOUNT_NAME - nazwa firmy
- ACCOUNT_TYPE - typ konta (Company/Individual)
- INDUSTRY - branża
- WEBSITE, PHONE, EMAIL - dane kontaktowe
- BILLING_ADDRESS, SHIPPING_ADDRESS - adresy
- OWNER_ID (FK) - właściciel w systemie

**Indeksy:**
- IDX_ACCOUNT_OWNER (OWNER_ID)
- IDX_ACCOUNT_NAME (ACCOUNT_NAME)
- IDX_ACCOUNT_TYPE (ACCOUNT_TYPE)

### CONTACTS
Kontakty/Osoby

**Kolumny:**
- ID_CONTACT (PK)
- ACCOUNT_ID (FK) - powiązanie z firmą (opcjonalne)
- FIRST_NAME, LAST_NAME - imię i nazwisko
- EMAIL - email (unikalny)
- PHONE, MOBILE - telefony
- TITLE - stanowisko
- DEPARTMENT - dział
- OWNER_ID (FK) - właściciel w systemie

**Indeksy:**
- IDX_CONTACT_ACCOUNT (ACCOUNT_ID)
- IDX_CONTACT_OWNER (OWNER_ID)
- IDX_CONTACT_EMAIL (EMAIL)
- IDX_CONTACT_NAME (LAST_NAME, FIRST_NAME)

### LEADS
Leady (potencjalni klienci)

**Kolumny:**
- ID_LEAD (PK)
- FIRST_NAME, LAST_NAME, COMPANY - dane
- EMAIL, PHONE - kontakt
- SOURCE_CODE (FK) - źródło leadu
- STATUS_CODE (FK) - status
- RATING - ocena (Hot/Warm/Cold)
- OWNER_ID (FK) - właściciel
- CONVERTED_TO_CONTACT_ID (FK) - konwersja do kontaktu

**Indeksy:**
- IDX_LEAD_SOURCE (SOURCE_CODE)
- IDX_LEAD_STATUS (STATUS_CODE)
- IDX_LEAD_OWNER (OWNER_ID)
- IDX_LEAD_RATING (RATING)
- **IDX_LEAD_OWNER_STATUS** (OWNER_ID, STATUS_CODE) - złożony dla list
- IDX_LEAD_CREATED (CREATED_AT DESC)

### OPPORTUNITIES
Szanse sprzedaży (Sales Pipeline)

**Kolumny:**
- ID_OPPORTUNITY (PK)
- OPPORTUNITY_NAME - nazwa szansy
- ACCOUNT_ID (FK), CONTACT_ID (FK) - powiązania
- STAGE_CODE (FK) - etap pipeline
- AMOUNT - wartość
- PROBABILITY - prawdopodobieństwo (%)
- EXPECTED_CLOSE_DATE - przewidywana data zamknięcia
- OWNER_ID (FK) - właściciel

**Indeksy:**
- IDX_OPP_ACCOUNT (ACCOUNT_ID)
- IDX_OPP_CONTACT (CONTACT_ID)
- IDX_OPP_STAGE (STAGE_CODE)
- IDX_OPP_OWNER (OWNER_ID)
- **IDX_OPP_OWNER_STAGE** (OWNER_ID, STAGE_CODE) - złożony dla dashboard
- IDX_OPP_CLOSE_DATE (EXPECTED_CLOSE_DATE)
- IDX_OPP_CREATED (CREATED_AT DESC)

### ACTIVITIES
Aktywności (Task, Meeting, Call, Email)

**Kolumny:**
- ID_ACTIVITY (PK)
- SUBJECT - temat
- TYPE_CODE (FK) - typ aktywności
- STATUS_CODE (FK) - status
- PRIORITY_CODE (FK) - priorytet
- DUE_DATE - termin
- COMPLETED_AT - data zakończenia
- RELATED_TO_TYPE, RELATED_TO_ID - powiązanie polimorficzne
- OWNER_ID (FK), ASSIGNED_TO_ID (FK) - właściciel i przypisany

**Indeksy:**
- IDX_ACTIVITY_TYPE (TYPE_CODE)
- IDX_ACTIVITY_STATUS (STATUS_CODE)
- IDX_ACTIVITY_PRIORITY (PRIORITY_CODE)
- IDX_ACTIVITY_OWNER (OWNER_ID)
- IDX_ACTIVITY_ASSIGNED (ASSIGNED_TO_ID)
- IDX_ACTIVITY_RELATED (RELATED_TO_TYPE, RELATED_TO_ID)
- **IDX_ACTIVITY_OWNER_STATUS_DUE** (OWNER_ID, STATUS_CODE, DUE_DATE) - złożony dla kalendarza
- IDX_ACTIVITY_DUE (DUE_DATE)

### NOTES
Notatki przypisane do różnych encji

**Kolumny:**
- ID_NOTE (PK)
- TITLE, CONTENT - tytuł i treść
- RELATED_TO_TYPE, RELATED_TO_ID - powiązanie polimorficzne
- CREATED_BY (FK) - autor

**Indeksy:**
- IDX_NOTE_RELATED (RELATED_TO_TYPE, RELATED_TO_ID)
- IDX_NOTE_CREATOR (CREATED_BY)
- IDX_NOTE_CREATED (CREATED_AT DESC)

### ATTACHMENTS
Załączniki do różnych encji

**Kolumny:**
- ID_ATTACHMENT (PK)
- FILE_NAME, FILE_PATH - nazwa i ścieżka pliku
- FILE_TYPE, FILE_SIZE - typ i rozmiar
- RELATED_TO_TYPE, RELATED_TO_ID - powiązanie polimorficzne
- UPLOADED_BY (FK) - kto dodał

**Indeksy:**
- IDX_ATTACHMENT_RELATED (RELATED_TO_TYPE, RELATED_TO_ID)
- IDX_ATTACHMENT_UPLOADER (UPLOADED_BY)

## Tabele Serwisowe (Moduł Warsztat)

### KLIENCI
Oryginalna tabela klientów warsztatowych

**Rozszerzenia:**
- CONTACT_ID (FK) - opcjonalne powiązanie z CONTACTS
- CREATED_AT - znacznik czasowy

**Indeksy:**
- IDX_KLIENT_CONTACT (CONTACT_ID)

### POJAZDY
Pojazdy klientów

**Rozszerzenia:**
- CREATED_AT - znacznik czasowy

**Indeksy:**
- IDX_POJAZD_KLIENT (ID_KLIENTA)

**Trigger:**
- SPRAWDZ_ROK_PRODUKCJI - walidacja roku produkcji (1900 - bieżący rok)

### ZLECENIA
Zlecenia serwisowe

**Rozszerzenia:**
- STATUS_CODE (FK) - nowe powiązanie ze słownikiem
- OWNER_ID (FK) - właściciel zlecenia
- CREATED_AT, UPDATED_AT - znaczniki czasowe

**Indeksy:**
- IDX_ZLECENIE_POJAZD (ID_POJAZDU)
- IDX_ZLECENIE_STATUS (STATUS_CODE)
- IDX_ZLECENIE_OWNER (OWNER_ID)

## Indeksy - Podsumowanie

### 1. Indeksy na Kluczach Obcych (FK)
Wszystkie klucze obce mają indeksy dla optymalizacji JOIN-ów:
- 22 indeksy FK w tabelach CRM
- 3 indeksy FK w tabelach serwisowych

### 2. Indeksy Złożone (Composite)
Dla typowych list i zapytań:
- **IDX_LEAD_OWNER_STATUS** - lista leadów po właścicielu i statusie
- **IDX_OPP_OWNER_STAGE** - lista szans po właścicielu i etapie
- **IDX_ACTIVITY_OWNER_STATUS_DUE** - kalendarz aktywności
- **IDX_CONTACT_NAME** - wyszukiwanie po nazwisku i imieniu

### 3. Indeksy Unikalne
Na kolumnach z unikalością biznesową:
- EMAIL w USERS, CONTACTS, KLIENCI
- USERNAME w USERS
- CODE we wszystkich słownikach (9 indeksów)
- NUMER_REJESTRACYJNY w POJAZDY

### 4. Indeksy Wydajnościowe
Na kolumnach często używanych w filtrowaniu:
- Daty utworzenia (DESC) dla sortowania
- Daty terminów dla kalendarzy
- Statusy i typy dla filtrowania

## Instalacja

### Krok 1: Przygotowanie
```sql
-- Połącz się jako użytkownik z uprawnieniami DBA
sqlplus SYSTEM/hasło@XEPDB1
```

### Krok 2: Wykonanie Skryptów
```sql
-- 1. Utwórz schemat
@01_schema.sql

-- 2. Załaduj dane słownikowe
@02_seed_dict.sql

-- 3. (Opcjonalnie) Zmigruj dane i dodaj przykłady
@03_migrate_existing.sql
```

### Krok 3: Weryfikacja
```sql
-- Sprawdź liczbę tabel
SELECT COUNT(*) FROM USER_TABLES;

-- Sprawdź indeksy
SELECT INDEX_NAME, TABLE_NAME, UNIQUENESS 
FROM USER_INDEXES 
ORDER BY TABLE_NAME, INDEX_NAME;

-- Sprawdź dane
SELECT 'USERS' AS TABELA, COUNT(*) AS LICZBA FROM USERS
UNION ALL
SELECT 'DICT_*', COUNT(*) FROM DICT_USER_STATUS
-- itd.
```

## Testowi Użytkownicy

Po wykonaniu seed, dostępni są użytkownicy:

| Username | Hasło | Rola | Email |
|----------|-------|------|-------|
| admin | admin123 | ADMIN | admin@warsztat.pl |
| sprzedawca | admin123 | SALES | sales@warsztat.pl |
| serwisant | admin123 | SERVICE | service@warsztat.pl |

**Uwaga:** Hasła są zahashowane SHA-256. W produkcji zmień hasła!

## Uwagi Bezpieczeństwa

1. **Hasła:** Używany SHA-256 - rozważ bcrypt lub Argon2 dla produkcji
2. **Uprawnienia:** Użyj dedykowanego użytkownika DB, nie SYSTEM
3. **Backup:** Regularnie twórz kopie zapasowe
4. **SQL Injection:** W PHP używaj tylko prepared statements z bindami
5. **Audyt:** Rozważ dodanie tabel audytowych dla krytycznych operacji

## Rozbudowa

### Możliwe Rozszerzenia
- Historia zmian (audit tables)
- Wersjonowanie rekordów
- Soft delete (zamiast fizycznego usuwania)
- Pełnotekstowe wyszukiwanie (Oracle Text)
- Partycjonowanie tabel dla dużych wolumenów
- Materialized views dla raportów

---

**Data aktualizacji:** 2026-01-21  
**Wersja:** 2.0  
**Autor:** System Wspomagania Warsztatu Samochodowego - CRM Edition
