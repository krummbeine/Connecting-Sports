// public/js/sportevent-kommentar.js

jQuery(function($) {
    $("#create-kommentar").on('click', function(event){
    	
        event.preventDefault();
        var $kommentar = $(this);
        $.post("chat/create-kommentar", null,
            function(data){
                if(data.response == true){
                    $kommentar.before("<div class=\"kommentar\"><textarea class=\"chatfeld\" id=\"kommentar-"+data.neue_kommentar_id
                    		+"\">Neues Kommentar</textarea><a href=\"#\" id=\"remove-"+data.neue_kommentar_id
                    		+"\"class=\"delete-kommentar\">X</a></div>");
                } else {
                    console.log('Kommentar: Erstellen fehlgeschlagen.');
                }
            }, 'json');

        //console.log('Kommentar-Erstellen aufgerufen');
    });

    $('#kommentare').on('click', 'a.delete-kommentar',function(event){
        event.preventDefault();
        var $kommentar = $(this);
        var remove_id = $(this).attr('id');
        remove_id = remove_id.replace("delete-","");

        // removeKommentarAction aufrufen -> 
        $.post("chat/remove-kommentar", {
            id: remove_id
        },
        function(data){
            if(data.response == true)
                $kommentar.parent().remove();
            else{
                console.log('Kommentar: Entfernen fehlgeschlagen.');
            }
        }, 'json');
    });

    $('#kommentare').on('keyup', 'textarea', function(event){
    	 
        var $kommentar = $(this);					// this == <textarea id="">
        
        // ID von Textarea auslesen, indem "kommentar-" weggelassen wird
        var update_id = $kommentar.attr('id'); 		// id == <textarea id="">
        update_id = update_id.replace("kommentar-","");
        
        // Textinhalt des Textarea
        update_inhalt = $kommentar.val();

        // Daten an den KommentarController &uuml;bergeben
        $.post("chat/update-kommentar", {
            id: update_id,
            inhalt: update_inhalt
        },
        function(data){
            if(data.response == false){ console.log('Kommentar: Aktualisieren fehlgeschlagen.'); }
        }, 
        'json');
    });
});
