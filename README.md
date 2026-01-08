# Progetto Tecweb - Prophit

Sito demo per formazione finanziaria con gestione corsi, area personale e pannello admin.

## Credenziali di default

- Admin: `admin` / `admin`
- Utente: `user` / `user`

Le credenziali devono essere così sennò sminchiamo gli strumenti automatici alla Gaggi.

## Avvio rapido

1) Avvio server PHP:
```
php -S localhost:8000 -t .
```

2) URL principali:
- Home: `http://localhost:8000/src/php/index.php`
- Corsi: `http://localhost:8000/src/php/corsi.php`
- Dettaglio corso: `http://localhost:8000/src/php/dettagliocorso.php?id=1`
- Area personale: `http://localhost:8000/src/php/areapersonale.php`
- Admin: `http://localhost:8000/src/php/admin.php`
- Contatti: `http://localhost:8000/src/php/contatti.php`

## Funzionalita principali

- Catalogo corsi con filtro per categoria.
- Dettaglio corso con acquisto gratis/elimina per utenti loggati.
- Recensioni: visibili a tutti, inseribili solo dopo acquisto.
- Area personale: elenco corsi acquistati.
- Admin:
  - Lista utenti con filtro live e preview cambiamenti admin.
  - Aggiunta corsi con upload immagine locale (jpg/png).
  - Categorie multiple via checkbox.
  - Storico acquisti con filtro live.
  - Messaggi contatti con contatore in testata.
- Contatti: form con invio a DB + link interattivi (mail/tel/maps).

## Note DB

Lo schema completo e i dati di default sono in:
- `src/sql/reset_default.sql`
- `src/sql/prophit.sql`
- `src/sql/dump.sql`
