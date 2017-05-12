var JSONP = (function () {
    var counter = 0,
        head, query, window = this;

    function load(url, error) {
        var script = document.createElement('script'),
            done = false;
        script.src = url;
        script.async = true;

        script.onload = script.onreadystatechange = function () {
            if (!done && (!this.readyState || this.readyState === "loaded" || this.readyState === "complete")) {
                done = true;
                script.onload = script.onreadystatechange = null;
                if (script && script.parentNode) {
                    script.parentNode.removeChild(script);
                }
            }
        };

        script.onerror = error;

        if (!head) {
            head = document.getElementsByTagName('head')[0];
        }
        head.appendChild(script);
    }

    function parameterscard(params, new_key, query) {
        params = params || {};
        for (var key in params) {
            if (params.hasOwnProperty(key)) {
                if (typeof params[key] !== 'object') {
                    query += "&" + new_key + "[" + encodeURIComponent(key) + "]=" + encodeURIComponent(params[key]);
                }
            }
        }
        return query;
    }

    function parameters(params, query) {
        params = params || {};
        for (var key in params) {
            if (params.hasOwnProperty(key)) {
                if (typeof params[key] !== 'object') {
                    query += "&" + encodeURIComponent(key) + "=" + encodeURIComponent(params[key]);
                } else {
                    query = parameterscard(params._card, key, query);
                }
            }
        }
        query += '&_=' + Math.round(Math.random() * 10000016);
        return query;
    }

    function jsonp(url, params, callback, error) {
        query = parameters(params, "");
        var jsonpcall = 'jsonp' + (++counter) + params.public_key + Math.round(Math.random() * 10000017) + '_' + Math.round(Math.random() * 1000001);
        window[jsonpcall] = function (data) {
            callback(data);
            try {
                delete window[jsonpcall];
            } catch (e) {}
            window[jsonpcall] = null;
        };

        load(url + '?' + 'callback=' + jsonpcall + query, error);
        return jsonpcall;
    }
    return {
        get: jsonp
    };
}());

var payU = (function () {
    "use strict";
    var url = "https://api.payulatam.com/payments-api/4.0/service";
    var img_url = "https://gateway.payulatam.com/ppp-web-gateway/images/tarjeta-credito/"
    var _input = 'payu-content';
    var _c = ['method', 'exp_month', 'exp_year', 'number', 'name_card', 'payer_id'];
    var msg = null;
    var card_method = null;
    var css_dropdown = null;
    var today = new Date();
    var response = {};
    var imgCount = 0;
    var _r_checked = false;
    var list_class = '_list_class_4567';
    var div_class = '_div_class_7897';
    var error = function () {
        if (msg === null) {
            msg = {};
            msg.es = {
                0: 'Esta franquicia no está disponible para ser utilizada.',
                1: 'Verifica que todos los datos de tu tarjeta de crédito son correctos',
                2: 'El documento debe tener entre 5 y 30 dígitos',
                3: 'Verifica que la fecha de expiración es válida.',
                4: 'No está configurado el Id del pagador Ej: <input payu-content="payer_id" value="PAYERID" type="hidden">',
                5: 'Debes seleccionar un método de pago.',
                6: 'Ha ocurrido un error inesperado con PayU.js. Esto puede ser a causa de problemas de conectividad, por favor intenta de nuevo. Si aún tienes problemas, por favor avísanos!',
                7: 'Tu configuración inicial es incorrecta.',
                8: 'Ha ocurrido un error inesperado con PayU.js.',
                9: 'Tipo de tarjeta de crédito desconocida',
                10: 'La tarjeta de crédito no pertenece a la franquicia seleccionada.',
                11: 'Elige tu franquicia',
                12: 'La llave pública utilizada no es válida  (Verifica payU.setPublicKey();)',
                13: 'No está configurada tu llave pública. Ej: payU.setPublicKey("TULLAVEPUBLICA");',
                14: 'No está configurada tu Cuenta Id. Ej: payU.setAccountID("1");',
                15: 'No está configurado el DIV de la franquicia. Ej: payU.setListBoxID("mylistID");',
                16: 'El identificador de cuenta utilizado no es válido (Verifica payU.setAccountID();)',
                17: 'Debes utilizar un identificador de cuenta y una llave pública válidos ( Verifica payU.setAccountID(); y payU.setPublicKey();)',
                18: 'Verifica que estas enviando los parámetros obligatorios en el Java Script (Nombre, Franquicia, No.tarjeta de crédito y fecha de expiración).',
                19: 'No tienes tarjeta crédito como medio de pago activo para tokenizar (Verifica que estás utilizando payU.getPaymentMethods();'
            };
            msg.pt = {
                0: 'Esta franquia não está disponível para ser utilizada.',
                1: 'Verifique se todos os dados do seu cartão de crédito são corretos.',
                2: 'O documento deve ter entre 5 a 30 dígitos.',
                3: 'Verifique que a data de validade é correta.',
                4: 'Não está configurado o ID do pagador Ex: <input payu-content="payer_id" value="PAYERID" type="hidden">',
                5: 'Você deve selecionar um método de pagamento.',
                6: 'Ocorreu um erro inesperado com PayU.js. Isto pode ser devido a problemas de conectividade, por favor, tente novamente. Se você ainda tiver problemas, por favor, avise-nos!',
                7: 'Sua configuiração inicial é incorreta.',
                8: 'Ocorreu um erro inesperado em Payu.',
                9: 'Tipo de cartão de crédito desconhecido.',
                10: 'O cartão de crédito não pertence à bandeira selecionada.',
                11: 'Selecione sua fraquia.',
                12: 'A chave pública usada é inválida válida  (Verificar payU.setPublicKey();)',
                13: 'Sua chave pública não está configurada  (Ex payU.setPublicKey("YOURPUBLICKEY");)',
                14: 'Sua conta ID não está configurada. Ex payU.setAccountID("1");',
                15: 'Não está configurado o DIV da fraquia Ex payU.setListBoxID("mylistID");',
                16: 'O identificador da conta não é valido ( Verifique  payU.setAccountID();)',
                17: 'Deve utilizar um identificador de conta e uma chave pública valida. (Verifique payU.setAccountID(); y payU.setPublicKey();)',
                18: 'Verifique se você está enviando os parâmetros necessários no Java Script (Nome, Franchise, número de cartão de crédito e data de validade).',
                19: 'Não tem o meio de pagamento de cartão de crédito ativado para tokenização (Verifique se está utilizando payU.getPaymentMethods();)'
            };
            msg.en = {
                0: 'This franchise is not available for use.',
                1: 'Please check your credit card data and make sure everything is correct.',
                2: 'The document must be 5 to 30 digits long.',
                3: 'Please check that the expiration date is valid.',
                4: 'The payer’s ID is not configurated Ex: <input payu-content="payer_id" value="PAYERID" type="hidden">',
                5: 'You must select a payment method.',
                6: 'An unexpected error has occurred with PayU.js. This could be due to connectivity issues, please try again. If you still have problems, please contact us!',
                7: 'The initial configuration is incorrect.',
                8: 'An unexpected error has occurred with PayU.js',
                9: 'Unknown credit card type.',
                10: 'The credit card does not match the franchise selected.',
                11: 'Select your franchise.',
                12: 'The public key your using is not valid (Check  payU.setPublicKey();)',
                13: 'Your public key is not configurated Ex: payU.setPublicKey("YOURPUBLICKEY");',
                14: 'Your Account ID is not configurated Ex: payU.setAccountID("1");',
                15: 'The franchise DIV is not configurated Ex: payU.setListBoxID("mylistID");',
                16: 'The account id your using is not valid (Check  payU.setAccountID();)',
                17: 'You must use a valid  account id and a public key (Check payU.setAccountID(); and payU.setPublicKey();)',
                18: 'Make sure you are sending the required parameters in Java Script (Name, Franchise, credit card number and expiration date)',
                19: 'You don’t have “credit card” as an active payments method for tokenization (Check you are using payU.getPaymentMethods();)'
            };
            return msg;
        }
        return msg;
    };
    var cssStyle = function () {
        if (css_dropdown === null) {
            var head = document.getElementsByTagName('head')[0];
            var style = document.createElement('style');
            style.appendChild(document.createTextNode('#' + list_class + ' {border:0px solid black; width:64px; height:63px; overflow:hidden; -moz-transition: height 0.1s; -webkit-transition: height 0.1s; -ms-transition: height 0.1s;  -o-transition: height 0.1s; transition: height 0.1s;}'));
            style.appendChild(document.createTextNode('#' + list_class + ' input {position:absolute;top:0;left:0;opacity:0;}'));
            style.appendChild(document.createTextNode('#' + list_class + ' label {display:none; margin:-1px; height:63px; opacity:0.2;}'));
            style.appendChild(document.createTextNode('#' + list_class + '.allowHover:hover label {display:block;} '));
            style.appendChild(document.createTextNode('#' + list_class + ' label:hover {opacity:0.5;}'));
            style.appendChild(document.createTextNode('#' + list_class + ' input:checked + label {opacity:1 !important; display:block;}'));
            style.appendChild(document.createTextNode('#trace {margin:0 0 20px;}'));
            style.type = 'text/css';
            head.appendChild(style);
            css_dropdown = style;
        }
    };
    var addStyle = function () {
        if (imgCount === 0) {
            imgCount = 3;
        }
        imgCount = imgCount * 63;
        css_dropdown.appendChild(document.createTextNode('#' + list_class + '.allowHover:hover {width:65px;height:' + imgCount + 'px; overflow-y:visible;-moz-transition: height 0.5s; -webkit-transition: height 0.5s; -ms-transition: height 0.5s;  -o-transition: height 0.5s;  transition: height 0.5s;}'));
    };
    var patterns = function () {
        if (card_method === null) {
            card_method = {
                'VISA': '^(4)(\\d{12}|\\d{15})$|^(606374\\d{10}$)',
                'MASTERCARD': '^(5[1-5]\\d{14}$)|^(2(?:2(?:2[1-9]|[3-9]\\d)|[3-6]\\d\\d|7(?:[01]\\d|20))\\d{12}$)',
                'AMEX': '^3[47][0-9]{13}$',
                'DINERS': '(^[35](?:0[0-5]|[68][0-9])[0-9]{11}$)|(^30[0-5]{11}$)|(^3095(\\d{10})$)|(^36{12}$)|(^3[89](\\d{12})$)', 
                'NARANJA': '^(589562)\\d{10}$',
                'SHOPPING': '(^603488(\\d{10})$)|(^2799(\\d{9})$)',
                'CABAL': '(^604(([23][0-9][0-9])|(400))(\\d{10})$)|(^589657(\\d{10})$)',
                'ARGENCARD': '^(501105|532362)(\\d{10}$)',
                'CENCOSUD': '^603493(\\d{10})$',
                'HIPERCARD': '^(384100|384140|384160|606282)(\\d{10}|\\d{13})$',
                'CODENSA': '^590712(\\d{10})$',
                'ELO': '(^(636368|438935|504175|451416|636297|650901|650485|650541|650700|650720|650720|650720|655021|650405)\\d{10})$|(^(5090|5067|4576|4011)\\d{12})$|(^(50904|50905|50906)\\d{11})$'
            };
        }
        return card_method;
    };
    var getPaymentData = function () {
        return {
            public_key: payU.public_key,
            account_id: payU.account_id,
            list_id: payU.list_id
        };
    };
    var getTokenData = function () {
        return {
            public_key: payU.public_key,
            account_id: payU.account_id,
            list_id: payU.list_id,
            _card: payU.card
        };
    };
    var validate = function (_d, _f, _s) {
        if (payU.error) {
            response.error = payU.error;
            _s(response);
            return false;
        }
        payU.card = {};
        var _in = _d.getElementsByTagName("input");
        for (var i = 0; i < _in.length; i++) {
            var _a = _in[i].getAttribute(_input);
            if (_a) {
                payU.card[_a] = _in[i].value;
            }
        }
        try {
        	 var _d = document.getElementById(list_class);
             var _div = document.getElementById(div_class);
             if (_d === null || _div === null) {
                 throw error()[payU.language][19];
             }

            number(payU.card.number);
            payer_id(payU.card.payer_id);
            expiryCheck(payU.card.exp_year, payU.card.exp_month);
            method_check();
        } catch (err) {
            response.error = err;
            _s(response);
            return false;
        }
        return true;
    };
    var handle_errors = function (data) {
        if (typeof data.error_code !== 'undefined') {
            payU.error = error()[payU.language][data.error_code];
            return true;
        }
        return false;
    };
    var paymentMethods = function () {
        sendData(url + '.payment', getPaymentData(), function (data) {
            // handle errors
            if (handle_errors(data)) {
                return;
            }
            if (payU.list_id === null) {
                // Exception form server TODO: delete
                payU.error = error()[payU.language][15];
                return;
            }

            cssStyle();

            var _main_div = document.getElementById(payU.list_id);
            if (_main_div === null) {
                payU.error = error()[payU.language][15];
                return;
            }

            var _d = document.createElement('div');
            _d.style.visibility = 'hidden';
            _d.style.position = 'absolute';
            _d.setAttribute('onmouseover', 'payU.showLabel(false)');
            _main_div.appendChild(_d);

            var _divLabel = document.createElement('div');

            if (payU.list_label) {
                _divLabel.innerHTML = payU.list_label;
            } else {
                _divLabel.innerHTML = error()[payU.language][11];
            }

            _divLabel.setAttribute('id', div_class);
            _divLabel.setAttribute('style', 'margin-top: 16px');
            _divLabel.style.position = 'absolute';


            _d.appendChild(_divLabel);

            var _div = document.createElement('div');
            _div.style.position = 'absolute';
            _div.setAttribute('id', list_class);
            _div.setAttribute('class', 'allowHover');
            _d.appendChild(_div);

            for (var i = 0; i < data.length; i++) {
                addDropdownItem(_div, data[i], ((data.length - 1) === (i)));
            }

        }, function () {
            payU.error = error()[payU.language][6];
        });
    };
    var showDivLabel = function (_b) {
        if (_b) {
            document.getElementById(div_class).style.display = 'block';
        } else {
            document.getElementById(div_class).style.display = 'none';
        }
    };
    var method_check = function () {
        var any_checked = false;
        var countRadios = 0;
        if (payU.card.method === null || payU.card.method === '') {
            var radios = document.getElementsByTagName('input');
            for (var i = 0; i < radios.length; i++) {
            	if (radios[i].type === 'radio') {
            		countRadios++;
            	}            		
                if (radios[i].type === 'radio' && radios[i].checked) {
                    // check if the card match the franchise
                    var patt = patterns();
                    if (patt[radios[i].id]) {
                        var test = new RegExp(patt[radios[i].id]);
                        if (!test.test(payU.card.number)) {
                            throw error()[payU.language][10];
                        }
                    }
                    any_checked = true;
                    payU.card.method = radios[i].id;
                }
            }
            // there are no radio buttons 
            if (countRadios === 0) {
            	throw error()[payU.language][19];
            }
            // no franchise is selected
            if (!any_checked) {
                showDivLabel(true);
                throw error()[payU.language][5];
            }
        }

    };
    var addCard = function (_k, _v) {
        payU.card[_k] = _v;
    };

    var payer_id = function (_v, j) {
        if (typeof j === 'undefined') {
            j = _c.indexOf('payer_id');
        }
        if (typeof _v === 'undefined') {
            throw error()[payU.language][4];
        }
        if (_v === '' || _v.lenght < 1) {
            throw error()[payU.language][4];
        }
        addCard(_c[j], _v);
    };
    var expiryCheck = function (_y, _m) {
        _y = _y.replace(/\D/g, '');
        _m = _m.replace(/\D/g, '');

        if (_m === '' || _y === '') {
            throw error()[payU.language][3];
        }
        if (!(_m >= 0 && _m < 13)) {
            throw error()[payU.language][3];
        }
        if (_y.length === 2) {
            _y = '20' + _y;
        }

        if (_m.length === 1) {
            _m = '0' + _m;
        }

        var formDate = new Date(_y, _m - 1);
        if (formDate < today) {
            throw error()[payU.language][3];
        }
        addCard('exp_year', _y);
        addCard('exp_month', _m);
    };
    var cardType = function (_v) {
        _v = _v.replace(/\D/g, '');
        var patt = patterns();
        for (var propt in patt) {
            if (patt.hasOwnProperty(propt)) {
                var test = new RegExp(patt[propt]);
                if (test.test(_v)) {
                    return propt;
                }
            }
        }
        return error()[payU.language][9];
    };
    var validateCardNumber = function (_v) {
        _v = _v.replace(/\D/g, '');
        if (_v === '' || (_v.length < 13 && _v.length > 20)) {
            showListbox(false);
            throw error()[payU.language][1];
        }
        disableListBox(false);
        var patt = patterns();
        payU.card.method = null;
        for (var propt in patt) {
            if (patt.hasOwnProperty(propt)) {
                var test = new RegExp(patt[propt]);
                if (test.test(_v)) {
                    var _r = document.getElementById(propt);
                    if (_r === null) {
                        throw error()[payU.language][0] + ' ' + propt;
                    }
                    _r_checked = true;
                    _r.checked = true;

                    payU.card.method = propt;
                    disableListBox(true);
                    showDivLabel(false);
                }
            }
        }
        showListbox(true);
        return _v;
    };
    var disableListBox = function (_b) {
        var _d = document.getElementById(list_class);
        _d.disabled = _b;
        var _r = _d.getElementsByTagName('*');
        for (var i = 0; i < _r.length; i++) {
            _r[i].disabled = _b;
        }
        if (_b) {
            _d.removeAttribute('class');
        } else {
            _d.setAttribute('class', 'allowHover');
        }
    };
    var showListbox = function (_b) {
        var _d = document.getElementById(list_class);
        var _div = document.getElementById(div_class);
        if (_d === null || _div === null) {
            throw error()[payU.language][19];
        }
        if (_b) {
            _d.style.visibility = 'visible';
            _div.style.visibility = 'visible';
        } else {
            _d.style.visibility = 'hidden';
            _div.style.visibility = 'hidden';
        }
    };
    var number = function (_v, j) {
        if (typeof j === 'undefined') {
            j = _c.indexOf('number');
        }
        addCard(_c[j], validateCardNumber(_v));
    };
    var imgExists = function (url, _d, _n, _last, _b) {
        var img = new Image();
        img.onerror = function () {
            _b(false, _d, _n, _last, url);
        };
        img.onload = function () {
            _b(true, _d, _n, _last, url);
        };
        img.src = url;
    };
    var addImage = function (exists, _d, _n, _last, _u) {
        if (exists) {
            var _o = document.createElement('input');
            _o.type = 'radio';
            _o.value = _n.id;
            _o.name = "line-style";
            _o.id = _n.name;
            _d.appendChild(_o);
            var _l = document.createElement('label');
            _l.setAttribute('for', _n.name);
            css_dropdown.appendChild(document.createTextNode('#' + list_class + ' label[for=' + _n.name + '] {background-image:url(' + _u + ');}'));
            _d.appendChild(_l);
            imgCount++;
        }
        if (_last) {
            addStyle();
        }
    };
    var sendData = function (_u, _d, _s, _e) {
        if (typeof jQuery === 'undefined') {
            JSONP.get(_u, _d, _s, _e);
        } else {
            jQuery.ajax({
                type: "POST",
                url: _u,
                success: _s,
                error: _e,
                dataType: "jsonp",
                data: _d,
                timeout: 1e4
            });
        }
    };
    var addDropdownItem = function (_d, _op, _last) { // dropdown!!
        var img = img_url + _op.name + '.png';
        imgExists(img, _d, _op, _last, addImage);
    };
    var qaURL = function (qaurl) {
        url = qaurl;
    };
    var qaURL_img = function (qaurl) {
        img_url = qaurl;
    };
    var radiosChecked = function () {
        if (_r_checked) {
            _r_checked = false;

            var radios = document.getElementsByTagName('input');
            for (var i = 0; i < radios.length; i++) {
                if (radios[i].type === 'radio') {
                    radios[i].checked = false;
                }
            }
        }
    };
    var starts = function (pk) {
        return pk.slice(0, 'PK'.length) === 'PK';
    };
    return {
        setURL: qaURL,
        setURL_img: qaURL_img,
        card: {},
        language: "es",
        setLanguage: function (lan) {
            if (lan === 'pt' || lan === 'en' || lan === 'es') {
                this.language = lan;
            }
        },
        setPublicKey: function (public_key) {
            if (typeof public_key === 'string') {

                this.public_key = public_key;
            }
        },
        setListBoxID: function (list_id, list_label) {
            if (typeof list_id === 'string') {
                this.list_id = list_id;
            }
            if (typeof list_label === 'string') {
                this.list_label = list_label;
            }
        },
        setAccountID: function (account_id) {
            if (typeof account_id === 'string') {
                this.account_id = account_id;
            }
        },
        setCardDetails: function (card) {
            if (typeof card === 'object') {
                this.card = card;
            }
        },
        validateNumber: validateCardNumber,
        validateCard: function (number) {
            try {
                radiosChecked();
                validateCardNumber(number);
                method_check();
                return true;
            } catch (err) {
                return false;
            }
        },
        validateExpiry: function (year, month) {
            try {
                expiryCheck(year, month);
                return true;
            } catch (err) {
                return false;
            }
        },
        createToken: function (_s, _f) {
            if (_f) {
                if (!validate(document, _f, _s)) {
                    return;
                }
            }
            var response = {};
            if (typeof payU.public_key === 'undefined') {
                response.error = error()[payU.language][12];
                _s(response);
                return;
            }

            if (!starts(payU.public_key)) {
                response.error = error()[payU.language][12];
                _s(response);
                return;
            }

            sendData(url + '.token', getTokenData(), function (data) {
                // handle errors
                var response = {};
                if (handle_errors(data)) {
                    response.error = payU.error;
                    delete payU.error;
                    _s(response);
                    return;
                }
                var _d = document.getElementById(list_class);
                var _div = document.getElementById(div_class);
                if (_d === null || _div === null) {
                    throw error()[payU.language][19];
                }

                _s(data);
            }, function () {
                payU.error = error()[payU.language][6];
                var response = {};
                response.error = payU.error;
                delete payU.error;
                _s(response);
            });
        },
        showLabel: showDivLabel,
        showPaymentMethods: showListbox,
        getPaymentMethods: paymentMethods,
        cardPaymentMethod: cardType

    };
}());