document.addEventListener('DOMContentLoaded', function(){
   const pictureInput = document.querySelector("#update_utilisateur_picture");
   const pictureLabel = document.querySelector(".custom-file-label");
   pictureInput.addEventListener('change', evt => {
      const { value } = evt.currentTarget;
      if (value) {
         const fileName = value.split("\\").pop() || value.split("/").pop();
         pictureLabel.innerHTML = fileName;
      }
   });
}, false);
