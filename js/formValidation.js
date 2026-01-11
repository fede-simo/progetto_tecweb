/**
     * chiave: nome dell'input che cerco
     * [0]: Prima indicazione per la compilazione dell'input
     * [1]: espressione regolare da controllare
     * [2]: Hint nel caso in cui input fornito sia sbagliato
**/

    var dettagli_form = { 
        "nome": ["Esempio: Mario",/^[a-zA-Z' ]{2,}$/,"Inserire un nome di lunghezza almeno 2. Non sono ammessi numeri."],
        "cognome": ["Esempio: Rossi",/^[a-zA-Z' ]{2,}$/,"Inserire un cognome di lunghezza almeno 2. Non sono ammessi numeri."],
        "data_di_nascita": ["",/^\d{4}-\d{2}-\d{2}$/,"Inserire una data corretta"],
        "username": ["Esempio: Mario04",/^[a-zA-Z0-9]{2,}$/,"Inserire un nome utente di lunghezza almeno 2. Non sono ammessi spazi."],
        "password": ["Almeno 8 caratteri, 1 numero e 1 carattere speciale",/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/,"Inserire una password di almeno 8 caratteri, di cui almeno un numero e un carattere speciale."],
        "confermaPassword": ["",/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/,"Le password non coincidono."],
    };
    
    function caricamento() {
        for (var key in dettagli_form) {
            var input = document.getElementById(key);
            messaggio(input,0);
            input.onblur = function () {validazioneCampo(this);};
        }
        var node = document.getElementById('register-form');
        node.onsubmit = function () {
            return validazioneForm(this);
        };
    }
        
    function validazioneCampo(input) {		
        var regex = dettagli_form[input.id][1];
        var text = input.value;

        var p = input.parentNode;
        
        if (p.children[2]) {
            p.removeChild(p.children[2]);
        }

        if (text.search(regex)!=0) {
            messaggio(input, 1);
            //input.focus();	//puo essere fastidioso
            //input.select(); 	//non da usare sempre, specialmente nei campi lunghi
            return false;
        }

        if (input.id === "confermaPassword") {
            var password = document.getElementById("password").value;
            if (text !== password) {
                messaggio(input, 1);
                return false;
            }
        }

        return true;
    }
        
    function validazioneForm() {
        var errori = true;

        for (var key in dettagli_form) {

            var input = document.getElementById(key);
            errori = validazioneCampo(input) && errori;
        }
        return errori;
    }
        
    function messaggio(input, mode) {
    /* mode = 0, modalità input
        mode = 1, modalità errore */

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
