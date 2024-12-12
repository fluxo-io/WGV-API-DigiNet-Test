# README: DigiNet Versand aus BNHO-Praxen Datenprüfung

Diese Anwendung prüft eingehende medizinische Daten im XML-Format auf Vollständigkeit und Konsistenz. Die Prüfungen erfolgen hierarchisch und decken mehrere Ebenen ab. Bei erfolgreicher Prüfung werden die Daten weitergeleitet.

---

## 1. Allgemeine Prüfungen
- **Eingehende Daten:**
  - Die XML-Daten werden geparst.
  - Bei ungültigem XML wird ein Fehler zurückgegeben.

---

## 2. Detaillierte Prüfungen pro Visite
Die Daten jeder *Visite* werden einzeln überprüft. Jede Prüfung erfolgt nur, wenn die übergeordnete Ebene erfolgreich bestanden wurde.

### 2.1. Visiten-ID
- Prüft, ob eine Visiten-ID vorhanden ist.
- Prüft, ob die Visiten-ID nicht `-NA-` lautet.

---

### 2.2. Therapie-Daten
- **Wird nur geprüft, wenn eine gültige Visiten-ID vorhanden ist.**
- **Enthält folgende Prüfungen:**
  - Patiententransfer (ob vorhanden und nicht `-NA-`).
  - Lost to Follow-Up (ob vorhanden und nicht `-NA-`).
  - Datum der Visite (ob vorhanden und nicht `-NA-`).
  - Eventzyklus (ob vorhanden und nicht `-NA-`).

---

### 2.3. Therapieansprechen
- **Wird nur geprüft, wenn Therapie-Daten vorhanden sind.**
- **Enthält folgende Prüfungen:**
  - Datum der Einschätzung.
  - Einschätzung des Therapieansprechens.
  - Basis der Einschätzung.
  - Zweitmalignom (ob vorhanden und nicht `-NA-`).
  - Krankenhausaufenthalt (ob vorhanden und nicht `-NA-`).

---

### 2.4. ECOG
- **Wird nur geprüft, wenn Therapieansprechen vorhanden ist.**
- **Enthält folgende Prüfungen:**
  - Datum der Befragung.
  - ECOG-Wert (ob vorhanden und nicht `-NA-`).

---

### 2.5. Vitalstatus
- **Wird nur geprüft, wenn ECOG-Daten vorhanden sind.**
- **Enthält folgende Prüfungen:**
  - Vitalstatus (ob vorhanden und nicht `-NA-`).
  - ePROs (ob vorhanden und nicht `-NA-`).

---

## 3. Logikprüfung
- **Krankenhausaufenthalt:**
  - Überprüft Widersprüche zwischen "Ja/Nein"-Antworten und dem Vorhandensein von Daten.

---

## 4. Ergebnis
Die Daten werden nur dann weiterverarbeitet, wenn alle Prüfungen erfolgreich bestanden wurden. Bei Fehlern wird eine detaillierte Liste aller fehlenden oder fehlerhaften Felder ausgegeben.
