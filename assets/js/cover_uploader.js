if (typeof(Uploader) == 'undefined'){
   Uploader = {}
}
Uploader.File = null;
Uploader.UseXHR = false;
Uploader.ShouldSend = false;
Uploader.Init = function(){
    try {
        document.querySelectorAll("#settings_profile_avatar_choose_input")[0].addEventListener("change", Uploader.XHR.change, false);
    } catch(e){
        Uploader.Form.init();
    }
};
Uploader.Request = function(){
    if (Uploader.ShouldSend == true){
        if (Uploader.UseXHR == true){
             Uploader.XHR.send();
        } else {
            Uploader.Form.submit();
        }
        return true;
    }
    return false;
};
Uploader.XHR = {
    method : player_root + "playlist_cover.php?id=" + playlist_id,
    obj : null,
    change : function(){
        if (this.files.length > 0){
            Uploader.File = this.files[0];
            Uploader.ShouldSend = true;
            Uploader.UseXHR = true;
            document.querySelectorAll("#settings_profile_avatar_choose")[0].className = "display_none";
            document.querySelectorAll("#settings_profile_avatar_filename")[0].innerHTML = Uploader.File.name;
            document.querySelectorAll("#settings_profile_avatar_filename")[0].className = "";
        }  
    },
    send : function(){
        try {
            formData = new FormData();  
        	formData.append("avatar", Uploader.File );  
            var xhr = new XMLHttpRequest();
			xhr.addEventListener("readystatechange", Uploader.XHR.response, false);
            xhr.open("post", Uploader.XHR.method, true);
    		xhr.send(formData);  
		} catch(e){
		    Uploader.Form.submit();
		}
    },
    progress : function(e){
        var percentage = Math.floor((e.loaded / e.total)*100);
    },
    response : function(){
        if (this.readyState == 4){
            if (this.status == 200){
                var json = JSON.parse(this.responseText);
                if (json.status_code == 200){
                    window.parent.EditPlaylist.Avatar.done(true, json);
                    Uploader.RefreshImage();
                } else {
                    window.parent.EditPlaylist.Avatar.done(false, null);
                }
            } else {
                window.parent.EditPlaylist.Avatar.done(false, null);
            }
        }
    }
};
Uploader.Form = {
    init : function(){
        document.getElementById("settings_profile_avatar_choose_input").onchange = Uploader.Form.change;
    },
    change : function(e){
        Uploader.ShouldSend = true;
        var fileName = "image";
        try {
            if (this.files.length > 0){
                fileName = this.files[0].name;
            } 
        } catch(e){}
            document.getElementById("settings_profile_avatar_choose").className = "display_none";
            document.getElementById("settings_profile_avatar_filename").innerHTML = fileName;
            document.getElementById("settings_profile_avatar_filename").className = "";
    },
    submit : function(){
        document.getElementById("file_form").submit();
    }
}
Uploader.RefreshImage = function(){
    var img = document.getElementById("settings_profile_avatar_img");
    var src = img.getAttribute("src");
    img.setAttribute("src", src+"?"+Math.random());
}
try {
    window.addEventListener("load", Uploader.Init, false);
} catch(e){
    Uploader.Form.init();
}
if (uploaded == true){
    window.parent.EditPlaylist.Avatar.done(true, loggedInUser.username);
}