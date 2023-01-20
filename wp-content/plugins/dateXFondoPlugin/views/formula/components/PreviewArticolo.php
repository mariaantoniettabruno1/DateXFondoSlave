<?php

namespace dateXFondoPlugin;

class PreviewArticolo

{
    public static function render_scripts()
    {
        ?>

        <script>

            $(document).ready(function (){
                $('#modalPreviewArticolo').on('show.bs.modal', function (event) {
                    const button = $(event.relatedTarget)
                    const id = button.attr("data-id")
                    const articolo = articoli.find(a => Number(a.id) === Number(id));
                    const modal = $(this)
                    if(!articolo){
                        modal.modal("hide")
                        return;
                    }
                    modal.find('.modal-title').text(articolo.nome_articolo)
                    const body = modal.find('.modal-body')
                    body.val("")
                    const fields = {
                        "id_articolo": "ID Articolo",
                        "nome_articolo": "Nome",
                        "sottotitolo_articolo": "Sottotitolo",
                        "descrizione_articolo": "Descrizione",
                        "fondo": "Fondo",
                        "descrizione_fondo": "Descrizione Fondo",
                        "sezione": "Sezione",
                        "sottosezione": "Sottosezione",
                        "nota": "Nota",
                        "link": "Link",
                    }
                    for(const field in fields){
                        body.append(`
                            <h6>${fields[field]}</h6>
                            <p>${articolo[field]}</p>
                            <hr>
                        `)
                    }
                })
            })


        </script>
        <?php
    }

    public static function render()
    {
        ?>
        <!-- Modal -->
        <div class="modal fade" id="modalPreviewArticolo" tabindex="-1" role="dialog" aria-labelledby="modalPreviewArticoloTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPreviewArticoloTitle">
                            ...
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="max-height: 80vh; overflow-y: auto">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    </div>
                </div>
            </div>
        </div>


        <?php
        self::render_scripts();
    }
}