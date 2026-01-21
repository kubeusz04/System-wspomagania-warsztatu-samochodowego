-- ============================================================================
-- DANE SEED DLA SŁOWNIKÓW CRM
-- Data: 2026-01-21
-- Wersja: 2.0
-- ============================================================================

-- ============================================================================
-- 1. DICT_USER_STATUS
-- ============================================================================
INSERT INTO DICT_USER_STATUS (CODE, NAME, DESCRIPTION) VALUES ('ACTIVE', 'Aktywny', 'Użytkownik aktywny w systemie');
INSERT INTO DICT_USER_STATUS (CODE, NAME, DESCRIPTION) VALUES ('INACTIVE', 'Nieaktywny', 'Użytkownik czasowo nieaktywny');
INSERT INTO DICT_USER_STATUS (CODE, NAME, DESCRIPTION) VALUES ('BLOCKED', 'Zablokowany', 'Użytkownik zablokowany przez administratora');

-- ============================================================================
-- 2. DICT_USER_ROLE
-- ============================================================================
INSERT INTO DICT_USER_ROLE (CODE, NAME, DESCRIPTION, PERMISSIONS) VALUES 
('ADMIN', 'Administrator', 'Pełne uprawnienia w systemie', 'all');

INSERT INTO DICT_USER_ROLE (CODE, NAME, DESCRIPTION, PERMISSIONS) VALUES 
('SALES', 'Handlowiec', 'Dostęp do CRM: leady, szanse, aktywności', 'crm.read,crm.write,crm.delete');

INSERT INTO DICT_USER_ROLE (CODE, NAME, DESCRIPTION, PERMISSIONS) VALUES 
('USER', 'Użytkownik', 'Podstawowe uprawnienia', 'crm.read');

INSERT INTO DICT_USER_ROLE (CODE, NAME, DESCRIPTION, PERMISSIONS) VALUES 
('SERVICE', 'Serwisant', 'Dostęp do modułu serwisowego', 'service.read,service.write');

-- ============================================================================
-- 3. DICT_LEAD_SOURCE
-- ============================================================================
INSERT INTO DICT_LEAD_SOURCE (CODE, NAME, DESCRIPTION) VALUES ('WEBSITE', 'Strona WWW', 'Lead z formularza kontaktowego na stronie');
INSERT INTO DICT_LEAD_SOURCE (CODE, NAME, DESCRIPTION) VALUES ('PHONE', 'Telefon', 'Lead z rozmowy telefonicznej');
INSERT INTO DICT_LEAD_SOURCE (CODE, NAME, DESCRIPTION) VALUES ('EMAIL', 'Email', 'Lead z korespondencji email');
INSERT INTO DICT_LEAD_SOURCE (CODE, NAME, DESCRIPTION) VALUES ('REFERRAL', 'Polecenie', 'Lead z polecenia istniejącego klienta');
INSERT INTO DICT_LEAD_SOURCE (CODE, NAME, DESCRIPTION) VALUES ('SOCIAL', 'Media Społecznościowe', 'Lead z Facebook, LinkedIn, itp.');
INSERT INTO DICT_LEAD_SOURCE (CODE, NAME, DESCRIPTION) VALUES ('ADVERTISING', 'Reklama', 'Lead z kampanii reklamowej');
INSERT INTO DICT_LEAD_SOURCE (CODE, NAME, DESCRIPTION) VALUES ('TRADE_SHOW', 'Targi', 'Lead z targów branżowych');
INSERT INTO DICT_LEAD_SOURCE (CODE, NAME, DESCRIPTION) VALUES ('OTHER', 'Inne', 'Inne źródło');

-- ============================================================================
-- 4. DICT_LEAD_STATUS
-- ============================================================================
INSERT INTO DICT_LEAD_STATUS (CODE, NAME, DESCRIPTION, DISPLAY_ORDER) VALUES 
('NEW', 'Nowy', 'Nowy lead, wymaga kwalifikacji', 1);

INSERT INTO DICT_LEAD_STATUS (CODE, NAME, DESCRIPTION, DISPLAY_ORDER) VALUES 
('CONTACTED', 'Skontaktowany', 'Nawiązano pierwszy kontakt', 2);

INSERT INTO DICT_LEAD_STATUS (CODE, NAME, DESCRIPTION, DISPLAY_ORDER) VALUES 
('QUALIFIED', 'Zakwalifikowany', 'Lead spełnia kryteria sprzedażowe', 3);

INSERT INTO DICT_LEAD_STATUS (CODE, NAME, DESCRIPTION, DISPLAY_ORDER) VALUES 
('CONVERTED', 'Przekonwertowany', 'Lead został przekształcony w kontakt/szansę', 4);

INSERT INTO DICT_LEAD_STATUS (CODE, NAME, DESCRIPTION, DISPLAY_ORDER) VALUES 
('UNQUALIFIED', 'Niezakwalifikowany', 'Lead nie spełnia kryteriów', 5);

INSERT INTO DICT_LEAD_STATUS (CODE, NAME, DESCRIPTION, DISPLAY_ORDER) VALUES 
('LOST', 'Stracony', 'Lead stracony', 6);

-- ============================================================================
-- 5. DICT_OPPORTUNITY_STAGE
-- ============================================================================
INSERT INTO DICT_OPPORTUNITY_STAGE (CODE, NAME, DESCRIPTION, PROBABILITY, DISPLAY_ORDER, IS_CLOSED) VALUES 
('PROSPECTING', 'Prospecting', 'Wstępna identyfikacja możliwości', 10, 1, 'N');

INSERT INTO DICT_OPPORTUNITY_STAGE (CODE, NAME, DESCRIPTION, PROBABILITY, DISPLAY_ORDER, IS_CLOSED) VALUES 
('QUALIFICATION', 'Kwalifikacja', 'Weryfikacja potrzeb i budżetu', 20, 2, 'N');

INSERT INTO DICT_OPPORTUNITY_STAGE (CODE, NAME, DESCRIPTION, PROBABILITY, DISPLAY_ORDER, IS_CLOSED) VALUES 
('NEEDS_ANALYSIS', 'Analiza Potrzeb', 'Szczegółowa analiza wymagań klienta', 30, 3, 'N');

INSERT INTO DICT_OPPORTUNITY_STAGE (CODE, NAME, DESCRIPTION, PROBABILITY, DISPLAY_ORDER, IS_CLOSED) VALUES 
('PROPOSAL', 'Oferta', 'Przygotowanie i prezentacja oferty', 50, 4, 'N');

INSERT INTO DICT_OPPORTUNITY_STAGE (CODE, NAME, DESCRIPTION, PROBABILITY, DISPLAY_ORDER, IS_CLOSED) VALUES 
('NEGOTIATION', 'Negocjacje', 'Negocjacje warunków i ceny', 70, 5, 'N');

INSERT INTO DICT_OPPORTUNITY_STAGE (CODE, NAME, DESCRIPTION, PROBABILITY, DISPLAY_ORDER, IS_CLOSED) VALUES 
('CLOSED_WON', 'Wygrana', 'Szansa została zamknięta - sukces', 100, 6, 'Y');

INSERT INTO DICT_OPPORTUNITY_STAGE (CODE, NAME, DESCRIPTION, PROBABILITY, DISPLAY_ORDER, IS_CLOSED) VALUES 
('CLOSED_LOST', 'Stracona', 'Szansa została zamknięta - porażka', 0, 7, 'Y');

-- ============================================================================
-- 6. DICT_ACTIVITY_TYPE
-- ============================================================================
INSERT INTO DICT_ACTIVITY_TYPE (CODE, NAME, DESCRIPTION, ICON) VALUES 
('TASK', 'Zadanie', 'Ogólne zadanie do wykonania', 'fa-tasks');

INSERT INTO DICT_ACTIVITY_TYPE (CODE, NAME, DESCRIPTION, ICON) VALUES 
('MEETING', 'Spotkanie', 'Spotkanie z klientem lub wewnętrzne', 'fa-users');

INSERT INTO DICT_ACTIVITY_TYPE (CODE, NAME, DESCRIPTION, ICON) VALUES 
('CALL', 'Rozmowa telefoniczna', 'Rozmowa telefoniczna z klientem', 'fa-phone');

INSERT INTO DICT_ACTIVITY_TYPE (CODE, NAME, DESCRIPTION, ICON) VALUES 
('EMAIL', 'Email', 'Korespondencja email', 'fa-envelope');

-- ============================================================================
-- 7. DICT_ACTIVITY_STATUS
-- ============================================================================
INSERT INTO DICT_ACTIVITY_STATUS (CODE, NAME, DESCRIPTION, IS_COMPLETED) VALUES 
('PLANNED', 'Zaplanowana', 'Aktywność zaplanowana na przyszłość', 'N');

INSERT INTO DICT_ACTIVITY_STATUS (CODE, NAME, DESCRIPTION, IS_COMPLETED) VALUES 
('IN_PROGRESS', 'W trakcie', 'Aktywność w trakcie realizacji', 'N');

INSERT INTO DICT_ACTIVITY_STATUS (CODE, NAME, DESCRIPTION, IS_COMPLETED) VALUES 
('COMPLETED', 'Zakończona', 'Aktywność zakończona', 'Y');

INSERT INTO DICT_ACTIVITY_STATUS (CODE, NAME, DESCRIPTION, IS_COMPLETED) VALUES 
('CANCELLED', 'Anulowana', 'Aktywność anulowana', 'Y');

-- ============================================================================
-- 8. DICT_PRIORITY
-- ============================================================================
INSERT INTO DICT_PRIORITY (CODE, NAME, DESCRIPTION, LEVEL) VALUES 
('LOW', 'Niski', 'Niski priorytet', 1);

INSERT INTO DICT_PRIORITY (CODE, NAME, DESCRIPTION, LEVEL) VALUES 
('MEDIUM', 'Średni', 'Średni priorytet', 2);

INSERT INTO DICT_PRIORITY (CODE, NAME, DESCRIPTION, LEVEL) VALUES 
('HIGH', 'Wysoki', 'Wysoki priorytet', 3);

INSERT INTO DICT_PRIORITY (CODE, NAME, DESCRIPTION, LEVEL) VALUES 
('URGENT', 'Pilny', 'Pilny priorytet - wymaga natychmiastowej uwagi', 4);

-- ============================================================================
-- 9. DICT_ORDER_STATUS (dla modułu serwisowego)
-- ============================================================================
INSERT INTO DICT_ORDER_STATUS (CODE, NAME, DESCRIPTION, DISPLAY_ORDER) VALUES 
('PENDING', 'Oczekuje', 'Zlecenie oczekuje na rozpoczęcie', 1);

INSERT INTO DICT_ORDER_STATUS (CODE, NAME, DESCRIPTION, DISPLAY_ORDER) VALUES 
('IN_PROGRESS', 'W trakcie', 'Zlecenie w trakcie realizacji', 2);

INSERT INTO DICT_ORDER_STATUS (CODE, NAME, DESCRIPTION, DISPLAY_ORDER) VALUES 
('COMPLETED', 'Zakończone', 'Zlecenie zakończone', 3);

INSERT INTO DICT_ORDER_STATUS (CODE, NAME, DESCRIPTION, DISPLAY_ORDER) VALUES 
('CANCELLED', 'Anulowane', 'Zlecenie anulowane', 4);

-- ============================================================================
-- 10. UŻYTKOWNIK TESTOWY (hasło: admin123)
-- Hasło zaszyfrowane SHA-256
-- ============================================================================
INSERT INTO USERS (USERNAME, PASSWORD_HASH, EMAIL, FIRST_NAME, LAST_NAME, ROLE_CODE, STATUS_CODE) VALUES 
('admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin@warsztat.pl', 'Admin', 'System', 'ADMIN', 'ACTIVE');

INSERT INTO USERS (USERNAME, PASSWORD_HASH, EMAIL, FIRST_NAME, LAST_NAME, ROLE_CODE, STATUS_CODE) VALUES 
('sprzedawca', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'sales@warsztat.pl', 'Jan', 'Kowalski', 'SALES', 'ACTIVE');

INSERT INTO USERS (USERNAME, PASSWORD_HASH, EMAIL, FIRST_NAME, LAST_NAME, ROLE_CODE, STATUS_CODE) VALUES 
('serwisant', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'service@warsztat.pl', 'Piotr', 'Nowak', 'SERVICE', 'ACTIVE');

-- ============================================================================
-- COMMIT
-- ============================================================================
COMMIT;

-- ============================================================================
-- KONIEC SEED DATA
-- ============================================================================
