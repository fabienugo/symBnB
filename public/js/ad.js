$('#add_images').click(function(){
    // Je récupère le numéro des futurs champs que je vais créer
    const index = +$('#widgets-counter').val();
    // Je récupère le protoype des entrées 
    const tmpl = $('#annonce_images').data('prototype').replace(/__name__/g , index);
    // J'injecte ce code au sein de la div
    $('#annonce_images').append(tmpl);
    // Modification de la valeur de l'index à chaque ajout d'une image
    $('#widgets-counter').val(index + 1);
    // Je gère le bouton supprimer
    handleDeleteButtons();
});


function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter() {
    const count = +$('#annonce_images div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter();
handleDeleteButtons();