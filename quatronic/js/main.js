// Funkcija za objavljivanje posta
function objavi(id){	
	event.preventDefault();
    var sati = $('#sat').val();
    var minuta = $('#minuta').val();
    var date = $('#date').val();
	$.post('./action.php', {action: 'objavi_post', id: id, sati: sati, minuta: minuta, date: date}, function(res){
		location.reload();
	});
}
// Funkcija za ukidanje objavljenog posta
function ukini_objava(id){	
	event.preventDefault();
	$.post('./action.php', {action: 'ukini_objava', id: id}, function(res){
		location.reload();
	});
}
// Funkcija za promovisanje posta
function promo(id){	
	event.preventDefault();
	$.post('./action.php', {action: 'promo_post', id: id}, function(res){
		location.reload();
	});
}

function ukini_promo(id){	
	event.preventDefault();
	$.post('./action.php', {action: 'ukini_promo_post', id: id}, function(res){
		location.reload();
	});
}

// Deditovanje texta
function format_text(tag) {
	event.preventDefault();
    el = document.getElementById('sadrzaj');
    var selectedText=document.selection?document.selection.createRange().text:el.value.substring(el.selectionStart,el.selectionEnd);
    var newText='['+tag+']'+selectedText+'[/'+tag+']';
    if(document.selection) document.selection.createRange().text=newText;
    else el.value=el.value.substring(0,el.selectionStart)+newText+el.value.substring(el.selectionEnd,el.value.length);
}

// Brisanje taska
function remove_task(id){
    if(confirm("Da li ste sigurni da želite obrisati zadatak?") == true){
        $.post('./action.php', {action: 'remove_task', id: id}, function(){
            location.reload();
        });
    }else{
        return false;
    }
}

// Zavrsi zadatak
function end_task(id){
	if(confirm("Da li ste sigurni da želite završite zadatak?") == true){
        $.post('./action.php', {action: 'end_task', id: id}, function(){
            location.reload();
        });
    }else{
        return false;
    }
}

// Otvori  zadatak
function open_task(id){
	if(confirm("Da li ste sigurni da želite zadatak označiti kao nezavršen?") == true){
        $.post('./action.php', {action: 'open_task', id: id}, function(){
            location.reload();
        });
    }else{
        return false;
    }
}