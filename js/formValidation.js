/**
     * chiave: nome dell'input che cerco
     * [0]: Prima indicazione per la compilazione dell'input
     * [1]: espressione regolare da controllare
     * [2]: Hint nel caso in cui input fornito sia sbagliato
**/

    var dettagli_form_reg = { 
        "nome": ["Esempio: Mario",/^[a-zA-Z' ]{2,30}$/,"Inserire un nome di lunghezza compresa fra 2 e 30 caratteri. Non sono ammessi numeri."],
        "cognome": ["Esempio: Rossi",/^[a-zA-Z' ]{2,30}$/,"Inserire un cognome di lunghezza compresa fra 2 e 30 caratteri. Non sono ammessi numeri."],
        "data_di_nascita": ["",/^\d{4}-\d{2}-\d{2}$/,"Inserire una data corretta"],
        "username": ["Esempio: Mario04",/^[a-zA-Z0-9]{2,30}$/,"Inserire un nome utente di lunghezza compresa fra 2 e 30 caratteri. Non sono ammessi spazi."],
        "password": ["Almeno 8 caratteri, 1 numero e 1 carattere speciale",/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/,"Inserire una password di almeno 8 caratteri, di cui almeno un numero e un carattere speciale."],
        "password_confirm": ["Le password devono coincidere",/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/,"Le password non coincidono."],
    };

    var dettagli_form_log = { 
        "username": ["Esempio: Mario04",/^[a-zA-Z0-9]{2,30}$/,"Inserire un nome utente di lunghezza compresa fra 2 e 30 caratteri. Non sono ammessi spazi."],
        "password": ["",/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/,"Inserire una password di almeno 8 caratteri, di cui almeno un numero e un carattere speciale."],
    };

    var dettagli_form_rec = { 
        "rating": ["Da 1 a 5",/^(?:[1-4](?:\.[0-9])?|5(?:\.0)?)$/,"Inserire una valutazione da 1 a 5. Massimo una cifra decimale."],
        "descrizione": ["Esempio: Ottimo corso",/^.{2,500}$/,"Inserire una recensione di lunghezza compresa fra 2 e 500 caratteri."],
    };

    var dettagli_form_contatti = { 
        "nome": ["Esempio: Mario",/^[a-zA-Z' ]{2,30}$/,"Inserire un nome di lunghezza compresa fra 2 e 30 caratteri. Non sono ammessi numeri."],
        "email": ["Esempio: mario@rossi.it",/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,"Inserire un indirizzo di posta elettronica valido. Esempio: mario@rossi.it"],
        "oggetto": ["Esempio: Create un nuovo corso",/^.{2,100}$/,"Inserire un oggetto lunghezza compresa fra 2 e 100 caratteri."],
        "messaggio": ["Esempio: Vorrei un nuovo corso",/^.{2,500}$/,"Inserire un messaggio di lunghezza compresa fra 2 e 500 caratteri."],
    };

    var dettagli_form_corsi = { 
        "titolo": ["Esempio: Corso di finanza",/^.{2,100}$/,"Inserire un titolo di lunghezza compresa fra 2 e 100 caratteri."],
        "immagine": ["(.jpg, .jpeg, .png, .webp)",/.*/,"Selezionare un file immagine valido con estensione .jpg, .jpeg, .png o .webp."],
        "categorie": ["",/^.{2,100}$/,"Inserire una categoria valida."],
        "durata": ["Durata in ore. Esempio: 10",/^[1-9][0-9]*$/,"Inserire una durata in ore maggiore di 0. Non sono ammesse cifre decimali."],
        "costo": ["Costo in euro. Minimo 0 e solo multipli di 5. Esempio: 100",/^(0|[5]|[1-9][0-9]*[05])$/,"Inserire un costo in euro. Minimo 0 e solo multipli di 5."],
        "modalita": ["",/^.{2,100}$/,"Inserire una modalità valida."],
        "breve_desc": ["Esempio: Corso di finanza",/^.{2,200}$/,"Inserire una breve descrizione di lunghezza compresa fra 2 e 200 caratteri."],
        "desc_completa": ["Esempio: Corso di finanza",/^.{2,500}$/,"Inserire una descrizione di lunghezza compresa fra 2 e 500 caratteri."],
    };
    
    function caricamentoReg() {
        for (var key in dettagli_form_reg) {
            var input = document.getElementById(key);
            messaggio(input,0,dettagli_form_reg);
            input.onblur = function () {validazioneCampo(this, dettagli_form_reg);};
        }
        var node = document.getElementById('register-form');
        node.onsubmit = function () {
            return validazioneForm(this, dettagli_form_reg);
        };
    }

    function caricamentoLog() {
        for (var key in dettagli_form_log) {
            var input = document.getElementById(key);
            messaggio(input,0,dettagli_form_log);
            input.onblur = function () {validazioneCampo(this, dettagli_form_log);};
        }
        var node = document.getElementById('login-form');
        node.onsubmit = function () {
            return validazioneForm(this, dettagli_form_log);
        };
    }

    function caricamentoRec() {
        for (var key in dettagli_form_rec) {
            var input = document.getElementById(key);
            messaggio(input,0,dettagli_form_rec);
            input.onblur = function () {validazioneCampo(this, dettagli_form_rec);};
        }
        var node = document.getElementById('rec-form');
        node.onsubmit = function () {
            return validazioneForm(this, dettagli_form_rec);
        };
    }

    function caricamentoModRec() {
        for (var key in dettagli_form_rec) {
            var input = document.getElementById(key);
            messaggio(input,0,dettagli_form_rec);
            input.onblur = function () {validazioneCampo(this, dettagli_form_rec);};
        }
        var node = document.getElementById('modifica-recensione-form');
        node.onsubmit = function () {
            return validazioneForm(this, dettagli_form_rec);
        };
    }

    function caricamentoContatti() {
        for (var key in dettagli_form_contatti) {
            var input = document.getElementById(key);
            messaggio(input,0,dettagli_form_contatti);
            input.onblur = function () {validazioneCampo(this, dettagli_form_contatti);};
        }
        var node = document.getElementById('contatti-form');
        node.onsubmit = function () {
            return validazioneForm(this, dettagli_form_contatti);
        };
    }

    function caricamentoCorsi() {
        for (var key in dettagli_form_corsi) {
            var input = document.getElementById(key);
            messaggio(input,0,dettagli_form_corsi);
            input.onblur = function () {validazioneCampo(this, dettagli_form_corsi);};
        }
        var node = document.getElementById('corsi-form');
        node.onsubmit = function () {
            return validazioneForm(this, dettagli_form_corsi);
        };
    }
        
    function validazioneCampo(input, dettagli_form) {	
        var text = input.value;

        if (dettagli_form === dettagli_form_log && input.id === "password") {
            var username = document.getElementById("username").value;
            if ((username === "admin" && text === "admin") || 
                (username === "user" && text === "user")) {
                return true;
            }
        }
        
        var regex = dettagli_form[input.id][1];
        var p = input.parentNode;
        
        if (p.children[2]) {
            p.removeChild(p.children[2]);
        }

        if (!regex.test(text)) {
            messaggio(input, 1, dettagli_form);
            return false;
        }

        if (input.id === "password_confirm") {
            var password = document.getElementById("password").value;
            if (text !== password) {
                messaggio(input, 1, dettagli_form);
                return false;
            }
        }

        return true;
    }
        
    function validazioneForm(form, dettagli_form) {
        var errori = true;
        for (var key in dettagli_form) {
            var input = document.getElementById(key);
            errori = validazioneCampo(input, dettagli_form) && errori;
        }
        return errori;
    }
        
    function messaggio(input, mode, dettagli_form) {
        var node;
        var p = input.parentNode;

        if (mode) {
            node = document.createElement('strong');
            node.className = 'errore';
            node.appendChild(document.createTextNode(dettagli_form[input.id][2]));	
        } else {
            node = document.createElement('span');
            node.className = 'suggerimento';
            node.appendChild(document.createTextNode(dettagli_form[input.id][0])); 
        }
        p.appendChild(node);
    
    }
