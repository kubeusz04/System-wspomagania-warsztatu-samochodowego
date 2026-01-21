-- ============================================================================
-- MIGRACJA ISTNIEJĄCYCH DANYCH
-- Data: 2026-01-21
-- Wersja: 2.0
-- ============================================================================
-- Ten skrypt pomaga zmigrować dane z istniejących tabel warsztatowych
-- do nowego modelu CRM (jeśli istnieją dane)
-- ============================================================================

-- ============================================================================
-- 1. MIGRACJA KLIENTÓW DO CONTACTS
-- Jeśli istnieją dane w starej tabeli KLIENCI, można je przekształcić
-- ============================================================================

-- Dodanie domyślnego użytkownika jako owner dla migrowanych danych
-- (zakładamy, że użytkownik admin już istnieje po seed)

-- Migracja klientów do CONTACTS (jeśli istnieje stara tabela i ma dane)
-- Uwaga: Ten skrypt jest opcjonalny - wykonaj tylko jeśli migr ujesz istniejące dane

/*
-- Sprawdź czy istnieją dane w KLIENCI
DECLARE
    v_count NUMBER;
BEGIN
    SELECT COUNT(*) INTO v_count FROM KLIENCI;
    
    IF v_count > 0 THEN
        -- Wstaw klientów jako kontakty
        INSERT INTO CONTACTS (FIRST_NAME, LAST_NAME, EMAIL, PHONE, OWNER_ID, CREATED_AT)
        SELECT 
            IMIE,
            NAZWISKO,
            EMAIL,
            TELEFON,
            (SELECT ID_USER FROM USERS WHERE USERNAME = 'admin'),
            NVL(CREATED_AT, SYSTIMESTAMP)
        FROM KLIENCI
        WHERE NOT EXISTS (
            SELECT 1 FROM CONTACTS WHERE EMAIL = KLIENCI.EMAIL
        );
        
        -- Aktualizuj KLIENCI z referencją do CONTACTS
        UPDATE KLIENCI K
        SET CONTACT_ID = (
            SELECT ID_CONTACT 
            FROM CONTACTS C 
            WHERE C.EMAIL = K.EMAIL
        )
        WHERE CONTACT_ID IS NULL;
        
        COMMIT;
        
        DBMS_OUTPUT.PUT_LINE('Zmigrowano ' || v_count || ' klientów do CONTACTS');
    ELSE
        DBMS_OUTPUT.PUT_LINE('Brak danych do migracji w KLIENCI');
    END IF;
END;
/
*/

-- ============================================================================
-- 2. AKTUALIZACJA ZLECEŃ - dodanie owner_id i status_code
-- ============================================================================

/*
-- Dodaj domyślnego ownera do istniejących zleceń
UPDATE ZLECENIA
SET OWNER_ID = (SELECT ID_USER FROM USERS WHERE USERNAME = 'serwisant')
WHERE OWNER_ID IS NULL;

-- Mapowanie starych statusów na nowe kody
UPDATE ZLECENIA SET STATUS_CODE = 'PENDING' WHERE STATUS = 'Oczekuje' AND STATUS_CODE IS NULL;
UPDATE ZLECENIA SET STATUS_CODE = 'IN_PROGRESS' WHERE STATUS = 'W trakcie' AND STATUS_CODE IS NULL;
UPDATE ZLECENIA SET STATUS_CODE = 'COMPLETED' WHERE STATUS = 'Zakończone' AND STATUS_CODE IS NULL;

COMMIT;
*/

-- ============================================================================
-- 3. PRZYKŁADOWE DANE TESTOWE DLA CRM
-- (Opcjonalne - do testowania aplikacji)
-- ============================================================================

-- Dodanie przykładowego konta (firmy)
INSERT INTO ACCOUNTS (ACCOUNT_NAME, ACCOUNT_TYPE, INDUSTRY, PHONE, EMAIL, OWNER_ID)
VALUES ('Auto Service Plus Sp. z o.o.', 'Company', 'Motoryzacja', '+48 22 123 4567', 'kontakt@autoservice.pl',
        (SELECT ID_USER FROM USERS WHERE USERNAME = 'sprzedawca'));

-- Dodanie przykładowych kontaktów
INSERT INTO CONTACTS (ACCOUNT_ID, FIRST_NAME, LAST_NAME, EMAIL, PHONE, TITLE, OWNER_ID)
VALUES ((SELECT ID_ACCOUNT FROM ACCOUNTS WHERE ACCOUNT_NAME = 'Auto Service Plus Sp. z o.o.'),
        'Anna', 'Wiśniewska', 'anna.wisniewska@autoservice.pl', '+48 601 234 567', 'Kierownik Zakupów',
        (SELECT ID_USER FROM USERS WHERE USERNAME = 'sprzedawca'));

INSERT INTO CONTACTS (FIRST_NAME, LAST_NAME, EMAIL, PHONE, MOBILE, OWNER_ID)
VALUES ('Marek', 'Zieliński', 'marek.zielinski@email.pl', '+48 12 345 6789', '+48 602 345 678',
        (SELECT ID_USER FROM USERS WHERE USERNAME = 'sprzedawca'));

-- Dodanie przykładowych leadów
INSERT INTO LEADS (FIRST_NAME, LAST_NAME, COMPANY, EMAIL, PHONE, SOURCE_CODE, STATUS_CODE, RATING, OWNER_ID, DESCRIPTION)
VALUES ('Tomasz', 'Kamiński', 'AutoMoto Serwis', 'tomasz@automoto.pl', '+48 603 456 789', 
        'WEBSITE', 'NEW', 'Hot', 
        (SELECT ID_USER FROM USERS WHERE USERNAME = 'sprzedawca'),
        'Zainteresowany współpracą w zakresie napraw powypadkowych');

INSERT INTO LEADS (FIRST_NAME, LAST_NAME, COMPANY, EMAIL, PHONE, SOURCE_CODE, STATUS_CODE, RATING, OWNER_ID)
VALUES ('Katarzyna', 'Lewandowska', 'Flota Trans', 'k.lewandowska@flotatrans.pl', '+48 604 567 890', 
        'PHONE', 'CONTACTED', 'Warm', 
        (SELECT ID_USER FROM USERS WHERE USERNAME = 'sprzedawca'));

-- Dodanie przykładowej szansy sprzedaży
INSERT INTO OPPORTUNITIES (OPPORTUNITY_NAME, ACCOUNT_ID, CONTACT_ID, STAGE_CODE, AMOUNT, PROBABILITY, 
                           EXPECTED_CLOSE_DATE, DESCRIPTION, OWNER_ID)
VALUES ('Kontrakt serwisowy - Auto Service Plus', 
        (SELECT ID_ACCOUNT FROM ACCOUNTS WHERE ACCOUNT_NAME = 'Auto Service Plus Sp. z o.o.'),
        (SELECT ID_CONTACT FROM CONTACTS WHERE EMAIL = 'anna.wisniewska@autoservice.pl'),
        'PROPOSAL', 50000.00, 50,
        ADD_MONTHS(SYSDATE, 1),
        'Roczny kontrakt na serwis floty 20 pojazdów',
        (SELECT ID_USER FROM USERS WHERE USERNAME = 'sprzedawca'));

-- Dodanie przykładowych aktywności
INSERT INTO ACTIVITIES (SUBJECT, TYPE_CODE, STATUS_CODE, PRIORITY_CODE, DUE_DATE, 
                        RELATED_TO_TYPE, RELATED_TO_ID, OWNER_ID, ASSIGNED_TO_ID, DESCRIPTION)
VALUES ('Spotkanie z Anną Wiśniewską', 'MEETING', 'PLANNED', 'HIGH',
        SYSTIMESTAMP + INTERVAL '3' DAY,
        'Opportunity', (SELECT ID_OPPORTUNITY FROM OPPORTUNITIES WHERE OPPORTUNITY_NAME = 'Kontrakt serwisowy - Auto Service Plus'),
        (SELECT ID_USER FROM USERS WHERE USERNAME = 'sprzedawca'),
        (SELECT ID_USER FROM USERS WHERE USERNAME = 'sprzedawca'),
        'Prezentacja oferty i omówienie szczegółów kontraktu');

INSERT INTO ACTIVITIES (SUBJECT, TYPE_CODE, STATUS_CODE, PRIORITY_CODE, DUE_DATE, 
                        RELATED_TO_TYPE, RELATED_TO_ID, OWNER_ID, ASSIGNED_TO_ID)
VALUES ('Połączenie follow-up z Tomaszem', 'CALL', 'PLANNED', 'MEDIUM',
        SYSTIMESTAMP + INTERVAL '1' DAY,
        'Lead', (SELECT ID_LEAD FROM LEADS WHERE EMAIL = 'tomasz@automoto.pl'),
        (SELECT ID_USER FROM USERS WHERE USERNAME = 'sprzedawca'),
        (SELECT ID_USER FROM USERS WHERE USERNAME = 'sprzedawca'));

-- Dodanie przykładowej notatki
INSERT INTO NOTES (TITLE, CONTENT, RELATED_TO_TYPE, RELATED_TO_ID, CREATED_BY)
VALUES ('Uwagi po rozmowie', 
        'Klient bardzo zainteresowany. Wymaga szczegółowej kalkulacji kosztów. Preferuje start od lutego.',
        'Opportunity', 
        (SELECT ID_OPPORTUNITY FROM OPPORTUNITIES WHERE OPPORTUNITY_NAME = 'Kontrakt serwisowy - Auto Service Plus'),
        (SELECT ID_USER FROM USERS WHERE USERNAME = 'sprzedawca'));

-- ============================================================================
-- COMMIT
-- ============================================================================
COMMIT;

-- ============================================================================
-- WERYFIKACJA MIGRACJI
-- ============================================================================

-- Pokaż statystyki po migracji
SELECT 'USERS' AS TABELA, COUNT(*) AS LICZBA FROM USERS
UNION ALL
SELECT 'ACCOUNTS', COUNT(*) FROM ACCOUNTS
UNION ALL
SELECT 'CONTACTS', COUNT(*) FROM CONTACTS
UNION ALL
SELECT 'LEADS', COUNT(*) FROM LEADS
UNION ALL
SELECT 'OPPORTUNITIES', COUNT(*) FROM OPPORTUNITIES
UNION ALL
SELECT 'ACTIVITIES', COUNT(*) FROM ACTIVITIES
UNION ALL
SELECT 'NOTES', COUNT(*) FROM NOTES
UNION ALL
SELECT 'KLIENCI', COUNT(*) FROM KLIENCI
UNION ALL
SELECT 'POJAZDY', COUNT(*) FROM POJAZDY
UNION ALL
SELECT 'ZLECENIA', COUNT(*) FROM ZLECENIA;

-- ============================================================================
-- KONIEC MIGRACJI
-- ============================================================================
