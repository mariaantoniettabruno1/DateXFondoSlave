<?php

namespace dateXFondoPlugin;

class FormulaCard

{
    public static function render_scripts()
    {
        ?>
        <style>
            .alert-formula-success {
                z-index: 3;
            }

            .alert-formula-wrong {
                z-index: 3;
            }
        </style>
        <script>

            function renderSectionInput() {
                $('#inputSelectSezioneFormula').html('<option>Seleziona Sezione</option>');
                Object.keys(sezioni).forEach(sez => {
                    $('#inputSelectSezioneFormula').append(`<option>${sez}</option>`);
                });
            }


            function renderSubsectionInput(section) {
                $('#inputSelectSottosezioneFormula').html('<option>Seleziona Sottosezione</option>');
                sezioni[section]?.forEach(ssez => {
                    $('#inputSelectSottosezioneFormula').append(`<option>${ssez}</option>`);
                });
            }

            let selectedInput = null;

            function insertIntoFormula(insert, moveCaret = 0) {
                if (!selectedInput) return;
                const id = selectedInput.attr("id");

                const t = selectedInput.val()
                const cursorPosition = document.getElementById(id).selectionStart ?? 0;

                if (cursorPosition) {
                    selectedInput.val(t.slice(0, cursorPosition) + insert + t.slice(cursorPosition));
                } else {
                    selectedInput.val(t + insert);
                }
                let nextPosition = cursorPosition + insert.length + moveCaret
                setCaretPosition(id, nextPosition)
            }

            function setCaretPosition(elemId, caretPos) {
                var elem = document.getElementById(elemId);

                if (elem != null) {
                    if (elem.createTextRange) {
                        var range = elem.createTextRange();
                        range.move('character', caretPos);
                        range.select();
                    } else {
                        if (elem.selectionStart) {
                            elem.focus();
                            elem.setSelectionRange(caretPos, caretPos);
                        } else
                            elem.focus();
                    }
                }
            }

            function insertDecurtazioneIntoFormula(type, idArticolo) {
                if (!selectedInput) return;
                const t = selectedInput.val()
                if (type === "%") {
                    selectedInput.val(`(${t}) * ${idArticolo} / 100`);
                } else {
                    selectedInput.val(`(${t}) > 0 ? (${t}) : - (${t})`);
                }
                selectedInput.focus();
            }

            let formulaId = 0

            function editFormula(fId) {
                const formula = formule.find(f => Number(f.id) === Number(fId));

                $('#inputSelectSezioneFormula').val(formula.sezione);
                renderSubsectionInput(formula.sezione);
                $('#inputSelectSottosezioneFormula').val(formula.sottosezione);
                $('#inputNomeFormula').val(formula.nome);
                $('#inputDescrizioneFormula').val(formula.descrizione);
                $('#inputCheckboxVisibileFormula').prop('checked', formula.visibile)
                formulaId = fId;
                if (Number(formula.condizione) === 0) {
                    $('#inputFormula').val(formula.formula);
                } else {
                    const [cond, vf] = formula.formula.split("?");
                    const [v, f] = vf.split(":");
                    $('#inputCondizione').val(cond);
                    $('#inputVero').val(v);
                    $('#inputFalso').val(f);
                }

            }

            function clearInputCondition() {
                $('#inputSelectSezioneFormula').prop('selectedIndex', 0);
                $('#inputSelectSottosezioneFormula').prop('selectedIndex', -1);
                $('#inputNomeFormula').val('');
                $('#inputDescrizioneFormula').val('');
                $('#inputCondizione').val('');
                $('#inputVero').val('');
                $('#inputFalso').val('');
                $('#inputCheckboxVisibileFormula').prop('checked', false);
            }

            function clearInputFormula() {
                $('#inputSelectSezioneFormula').prop('selectedIndex', 0);
                $('#inputSelectSottosezioneFormula').prop('selectedIndex', -1);
                $('#inputNomeFormula').val('');
                $('#inputDescrizioneFormula').val('');
                $('#inputFormula').val('');
                $('#inputCheckboxVisibileFormula').prop('checked', false);
            }

            function validateArticoliFormula(formula) {
                articoli.forEach(articolo => {
                    if (formula.includes(articolo.id_articolo))
                        window[articolo.id_articolo] = 1;
                });
                formule.forEach(form => {
                    if (formula.includes(form.nome))
                        window[form.nome] = 1;
                });
                try {
                    eval(formula);
                    return 1;
                } catch (e) {
                    console.log("Formula non composta correttamente");
                    console.error(e);
                    return 0;
                }
            }


            $(document).ready(function () {
                clearInputFormula();
                clearInputCondition();
                renderSectionInput();
                $('#inputSelectSezioneFormula').change(function () {
                    const section = $('#inputSelectSezioneFormula').val();
                    if (section !== 'Seleziona Sezione') {
                        $('#inputSelectSottosezioneFormula').attr('disabled', false);
                        renderSubsectionInput(section);
                    } else {
                        $('#inputSelectSottosezioneFormula').attr('disabled', true);
                        $('#inputSelectSottosezioneFormula').html('');
                    }
                });

                $(".input-field").click(function () {
                    selectedInput = $(this);
                })

                $(".input-button").click(function () {
                    const insert = $(this).attr("data-input");
                    let moveCaret = $(this).attr("data-move-caret");
                    if (moveCaret) moveCaret = Number(moveCaret)
                    insertIntoFormula(insert, moveCaret);
                })

                $('#insertFormula').click(function () {
                    $("#errorNameFormula").attr('style', 'display:none');
                    let sezione = $('#inputSelectSezioneFormula').val();
                    let sottosezione = $('#inputSelectSottosezioneFormula').val();
                    let nome = $('#inputNomeFormula').val();
                    let descrizione = $('#inputDescrizioneFormula').val();
                    let formula = $('#inputFormula').val();
                    let formula_template_name = $('#inputSelectOwner').val();
                    let visibile = $('#inputCheckboxVisibileFormula').prop('checked') ? 1 : 0;
                    let bold = $('#boldCheck').prop('checked') ? 1 : 0;
                    let highText = $('#higherTextCheck').prop('checked') ? 1 : 0;
                    let text_type = '';
                    text_type = text_type + bold + highText;

                    if (sezione === 'Seleziona Sezione') {
                        sezione = null;
                    }
                    if (sottosezione === 'Seleziona Sottosezione') {
                        sottosezione = null;
                    }
                    let condizione = 0;
                    if (formule.find(form => form.nome === nome) === undefined){
                        const payload = {
                            id: formulaId,
                            sezione,
                            sottosezione,
                            nome,
                            descrizione,
                            formula,
                            visibile,
                            condizione,
                            text_type,
                            formula_template_name
                        }
                        if (!validateArticoliFormula(formula)) {
                            $(".alert-validate-wrong").show();
                            $(".alert-validate-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                $(".alert-validate-wrong").slideUp(500);
                            });
                        } else {
                            $.ajax({
                                url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/formula',
                                data: payload,
                                type: "POST",
                                success: function (response) {
                                    console.log(response);
                                    if (!response.updated) {
                                        if (response["id"]) {
                                            formule.push({...payload, id: response["id"]});
                                        }
                                        $(".alert-formula-success").show();
                                        $(".alert-formula-success").fadeTo(2000, 500).slideUp(500, function () {
                                            $(".alert-formula-success").slideUp(500);
                                        });
                                    } else {
                                        formule = formule.filter(f => Number(f.id) !== Number(formulaId));
                                        formule.push({...payload, id: response["id"]});
                                        $(".alert-formula-update-success").show();
                                        $(".alert-formula-update-success").fadeTo(2000, 500).slideUp(500, function () {
                                            $(".alert-formula-update-success").slideUp(500);
                                        });
                                    }
                                    formulaId = 0;
                                    handleFilter();
                                    clearInputFormula();
                                },
                                error: function (response) {
                                    console.error(response);
                                    $(".alert-formula-wrong").show();
                                    $(".alert-formula-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                        $(".alert-formula-wrong").slideUp(500);
                                    });
                                }
                            })
                        }
                    }
                    else{
                        $("#errorNameFormula").attr('style', 'display:block');
                    }



                });

                $('#insertCondition').click(function () {
                    {
                        let sezione = $('#inputSelectSezioneFormula').val();
                        let sottosezione = $('#inputSelectSottosezioneFormula').val();
                        let nome = $('#inputNomeFormula').val();
                        let descrizione = $('#inputDescrizioneFormula').val();
                        let cond = $('#inputCondizione').val();
                        let vero = $('#inputVero').val();
                        let falso = $('#inputFalso').val();
                        let formula_template_name = $('#inputSelectOwner').val();
                        let formula = `(${cond})?(${vero}):(${falso})`;
                        let visibile = $('#inputCheckboxVisibileFormula').prop('checked') ? 1 : 0;
                        if (sezione === 'Seleziona Sezione') {
                            sezione = null;
                        }
                        if (sottosezione === 'Seleziona Sottosezione') {
                            sottosezione = null;
                        }
                        let condizione = 1;

                        const payload = {
                            id: formulaId,
                            sezione,
                            sottosezione,
                            nome,
                            descrizione,
                            formula,
                            visibile,
                            condizione,
                            formula_template_name
                        }
                        $.ajax({
                            url: '<?= DateXFondoCommon::get_website_url() ?>/wp-json/datexfondoplugin/v1/formula',
                            data: payload,
                            type: "POST",
                            success: function (response) {
                                console.log(response);
                                if (!response.updated) {
                                    if (response["id"]) {
                                        formule.push({...payload, id: response["id"]});
                                    }
                                } else {
                                    formule = formule.filter(f => Number(f.id) !== Number(formulaId));
                                    formule.push({...payload, id: response["id"]});
                                }
                                formulaId = 0;
                                handleFilter();
                                clearInputCondition();
                                $(".alert-formula-success").show();
                                $(".alert-formula-success").fadeTo(2000, 500).slideUp(500, function () {
                                    $(".alert-formula-success").slideUp(500);
                                });
                            },
                            error: function (response) {
                                console.error(response);
                                $(".alert-formula-wrong").show();
                                $(".alert-formula-wrong").fadeTo(2000, 500).slideUp(500, function () {
                                    $(".alert-formula-wrong").slideUp(500);
                                });
                            }
                        })
                    }
                });
            });


        </script>
        <?php
    }

    public static function render()
    {
        ?>
        <div class="card">
            <div class="card-header">
                Informazioni Formula
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-4">
                        <input type="text" class="form-control" id="inputNomeFormula"
                               placeholder="Inserisci nome"
                               aria-label="NomeFormula" aria-describedby="basic-addon1">
                        <small id="errorNameFormula" class="form-text text-danger" style="display: none">Nome formula già presente</small>
                    </div>
                    <div class="col-8">
                        <input type="text" class="form-control" id="inputDescrizioneFormula"
                               placeholder="Inserisci descrizione" aria-label="DescrizioneFormula"
                               aria-describedby="basic-addon1"></div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="form-group form-check">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="boldCheck">
                                <label class="form-check-label" for="boldCheck">
                                    Grassetto
                                </label>

                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="higherTextCheck">
                                <label class="form-check-label" for="higherTextCheck">
                                    Testo Grande </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <select class="custom-select" id="inputSelectSezioneFormula">
                        </select>
                    </div>
                    <div class="col">
                        <select class="custom-select" id="inputSelectSottosezioneFormula" disabled>
                        </select>
                    </div>
                </div>
                <div class="row pb-2">
                    <div class="col">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="true"
                                   id="inputCheckboxVisibileFormula">
                            <label class="form-check-label" for="defaultCheck1">
                                Visibile
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr/>
        <div id="accordion">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne"
                                aria-expanded="true" aria-controls="collapseOne">
                            Formula
                        </button>
                    </h5>
                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col">
                                <input type="text" class="form-control input-field" id="inputFormula"
                                       placeholder="Inserisci formula"
                                       aria-label="Formula" aria-describedby="basic-addon1"></div>
                        </div>
                        <div class="d-flex flex-row justify-content-end">
                            <button class="btn btn-outline-primary" id="insertFormula">Salva Formula</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo"
                                aria-expanded="false" aria-controls="collapseTwo">
                            Condizionale
                        </button>
                    </h5>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col">
                                <input type="text" class="form-control input-field" id="inputCondizione"
                                       placeholder="Inserisci condizione" aria-label="Condizione"
                                       aria-describedby="basic-addon1"></div>
                        </div>
                        <div class="row pb-2">
                            <div class="col"><input type="text" class="form-control input-field" id="inputVero"
                                                    placeholder="Se vero"
                                                    aria-label="CondizionaleVero" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="row pb-2">
                            <div class="col">
                                <input type="text" class="form-control input-field" id="inputFalso"
                                       placeholder="Se falso"
                                       aria-label="CondizionaleFalso" aria-describedby="basic-addon1">
                            </div>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <button class="btn btn-outline-primary" id="insertCondition">Salva Condizionale</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="card mb-2">

            <div class="card-body">
                <div class="row mb-3" id="arithmeticControls">
                    <div class="col px-1">
                        <button id="btnPlus" class="btn btn-block btn-outline-primary input-button" data-input=" + ">
                            +
                        </button>
                    </div>
                    <div class="col px-1">
                        <button id="btnMinus" class="btn btn-block btn-outline-primary input-button" data-input=" - ">
                            -
                        </button>
                    </div>
                    <div class="col px-1">
                        <button id="btnTimes" class="btn btn-block btn-outline-primary input-button" data-input=" * ">
                            x
                        </button>
                    </div>
                    <div class="col px-1">
                        <button id="btnDiv" class="btn btn-block btn-outline-primary input-button" data-input=" / ">
                            /
                        </button>
                    </div>
                    <div class="col px-1">
                        <button class="btn btn-block btn-outline-primary input-button" data-input=" 0 "> 0</button>
                    </div>

                </div>
                <div class="row mb-3">
                    <div class="col px-1">
                        <button id="btnAnd" class="btn btn-block btn-outline-primary input-button" data-input=" && ">
                            E
                        </button>
                    </div>
                    <div class="col px-1">
                        <button id="btnOr" class="btn btn-block btn-outline-primary input-button" data-input=" || ">
                            O
                        </button>
                    </div>
                    <div class="col px-1">
                        <button id="btnNot" class="btn btn-block btn-outline-primary input-button" data-input=" !(   ) "
                                data-move-caret="-3">
                            NON
                        </button>
                    </div>
                    <div class="col px-1">
                        <button id="btnPar" class="btn btn-block btn-outline-primary input-button"
                                data-input=" (   ) " data-move-caret="-3">( )
                        </button>
                    </div>
                    <div class="col px-1">
                        <button class="btn btn-block btn-outline-primary input-button" data-input=" == "> =</button>
                    </div>
                </div>
                <div class="row" id="comparators">
                    <div class="col px-1">
                        <button class="btn btn-block btn-outline-primary input-button" data-input=" > ">></button>
                    </div>
                    <div class="col px-1">
                        <button class="btn btn-block btn-outline-primary input-button" data-input=" < "><</button>
                    </div>
                    <div class="col px-1">
                        <button class="btn btn-block btn-outline-primary input-button" data-input=" >= "> ≥</button>
                    </div>
                    <div class="col px-1">
                        <button class="btn btn-block btn-outline-primary input-button" data-input=" <= "> ≤</button>
                    </div>

                    <div class="col px-1">
                        <button class="btn btn-block btn-outline-primary input-button" data-input=" != "> ≠</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-success alert-formula-success" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Nuova formula aggiunta correttamente!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="alert alert-success alert-formula-update-success" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Formula aggiornata correttamente!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="alert alert-danger alert-formula-wrong" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Operazione non andata a buon fine
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="alert alert-danger alert-validate-wrong" role="alert"
             style="position:fixed; top: <?= is_admin_bar_showing() ? 47 : 15 ?>px; right: 15px; display:none">
            Formula non scritta correttamente
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
        self::render_scripts();
    }
}